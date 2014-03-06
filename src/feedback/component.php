<?php

/*
 * This file is part of the Studio Fact package.
 *
 * (c) Kulichkin Denis (onEXHovia) <onexhovia@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\ConfigurationException;
use Bitrix\Main\Entity;

global $APPLICATION;

Loc::loadMessages(__FILE__);

if (!class_exists('CIBlockElement')) {
	Loader::includeModule('iblock');
}

$application = Application::getInstance();
// Instance for old application
$applicationOld = &$APPLICATION;

$isAjax = (getenv('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest') ?: false;
$componentId = CAjax::GetComponentID($this->getName(), $this->getTemplateName());
$request = $application->getContext()->getRequest();
$componentAjax = false;

// If after adding a redirect occurred at the same page 
if (array_key_exists(sprintf('feedback_success_%s', $componentId), $_SESSION)) {
	unset($_SESSION[sprintf('feedback_success_%s', $componentId)]);
	$success = true;
}

// For the current component ajax request?
if ($isAjax) {
	$componentAjax = function() use($componentId, $request) {
		if (!$request->isPost() || !$request->getPost('ajax_id')) {
			return false;
		}
		
		return ($request->getPost('ajax_id') == $componentId);
	};
}

// Checking information block
if (is_numeric($arParams['IBLOCK_ID'])) {
	$arParams['IBLOCK_ID'] = (int)$arParams['IBLOCK_ID'];
	if ($arParams['CHECK_IBLOCK'] == 'Y') {
		$entityIblock = new Entity\Query(Entity\Base::getInstance('Bitrix\Iblock\IblockTable'));
		$entityIblock
			->setSelect(array('ID', 'NAME'))
			->setFilter(array('ID' => $arParams['IBLOCK_ID']))
			->setOrder(array('ID' => 'ASC'))
			->setLimit(1);
		
		$iblockResult = $entityIblock->exec()->fetch();
		if ($iblockResult === false) {
			throw new ConfigurationException(sprintf('Iblock with ID = %d not found', $arParams['IBLOCK_ID']));
		}
	}
} else {
	throw new ConfigurationException(sprintf('Invalid IBLOCK_ID param'));
}

// Reload captcha
// Return new code for captcha
if ($arParams['USE_CAPTCHA'] == 'Y' && $request->getPost('feedback_captcha_remote') && $componentAjax) {
	$applicationOld->RestartBuffer();
	header('Content-Type: application/json');
	exit(json_encode(array('captcha' => $applicationOld->CaptchaGetCode())));
}

if ($arParams['PHONE_REGEXP'] == '') {
	// @see http://habrahabr.ru/post/110731/
	$arParams['PHONE_REGEXP'] = '/^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$/';
}

// Validatation data in a form
if ($request->isPost() && $request->getPost(sprintf('send_form_%s', $componentId))) {
	$postData = $request->getPostList()->toArray();
	$postData = array_map('strip_tags', $postData);
	
	if ($postData['name'] == '') {
		$errorList['name'] = Loc::getMessage('ERROR_NAME');
	}
	
	if ($postData['message'] == '') {
		$errorList['message'] = Loc::getMessage('ERROR_MESSAGE');
	}
	
	if (!filter_var($postData['email'], FILTER_VALIDATE_EMAIL)) {
		$errorList['email'] = Loc::getMessage('ERROR_EMAIL');
	}
	
	if (!preg_match($arParams['PHONE_REGEXP'], $postData['phone'])) {
		$errorList['phone'] = Loc::getMessage('ERROR_PHONE');
	}
	
	if ($arParams['USE_CAPTCHA'] == 'Y') {
		if (!$applicationOld->CaptchaCheckCode($postData['captcha_word'], $postData['captcha_sid'])) {
			$errorList['captcha_word'] = Loc::getMessage('ERROR_CAPTCHA');
		}
	}
	
	if (!isset($errorList)) {
		$iblockElement = new CIBlockElement();
		$elementResult = $iblockElement->add(array(
			'IBLOCK_ID' => $arParams['IBLOCK_ID'],
			'NAME' => sprintf('%s %s', Loc::getMessage('POSTED_BY'), $postData['name']),
			'DETAIL_TEXT' => $postData['message'],
			'DETAIL_TYPE' => 'TEXT',
			'PROPERTY_VALUES' => array(
				'EMAIL' => $postData['email'],
				'PHONE' => $postData['phone']
			)
		));
	
		$success = (is_numeric($elementResult)) ? true : false;
		$internal = ($success === false) ? true : false;
		
		if (strlen($arParams['REDIRECT_PATH']) > 0 && $success && $arParams['AJAX'] != 'Y') {
			LocalRedirect($arParams['REDIRECT_PATH']);
		} elseif ($success && $arParams['AJAX'] != 'Y') {
			$redirectPath = $application
				->getContext()
				->getServer()
				->getRequestUri();

			$_SESSION[sprintf('feedback_success_%s', $componentId)] = true;
			LocalRedirect($redirectPath);
		}
	}
	
	// If enabled ajax mod and action in the request feedback_remote
	// Return the validation form in the format json
	if ($arParams['AJAX'] == 'Y' && $request->getPost('feedback_remote') && $componentAjax) {
		$applicationOld->RestartBuffer();
		header('Content-Type: application/json');
		
		$jsonResponse = array(
			'success' => $success ?: false,
			'errors' => $errorList ?: array(),
			'internal' => $internal ?: false,
			'use_redirect' => (strlen($arParams['REDIRECT_PATH']) > 0) ?: false,
			'redirect_path' => $arParams['REDIRECT_PATH'],
		);
		
		if ($arParams['USE_CAPTCHA'] == 'Y') {
			$jsonResponse['captcha'] = $applicationOld->CaptchaGetCode();
		}

		exit(json_encode($jsonResponse));
	}
}

$postData = array_map('htmlspecialchars', (isset($postData)) ? $postData : array());
$arResult = array(
	'IS_POST' => $request->isPost(),
	'SUCCESS' => $success ?: false,
	'INTERNAL' => $internal ?: false,
	'ERRORS' => $errorList ?: array(),
	'FORM' => array(
		'COMPONENT_ID' => $componentId,
		'NAME' => array_key_exists('name', $postData) ? $postData['name'] : '',
		'PHONE' => array_key_exists('phone', $postData) ? $postData['phone'] : '',
		'EMAIL' => array_key_exists('email', $postData) ? $postData['email'] : '',
		'MESSAGE' => array_key_exists('message', $postData) ? $postData['message'] : '',
	)
);

if ($arParams['USE_CAPTCHA'] == 'Y') {
	$arResult['CAPTCHA_CODE'] = $applicationOld->CaptchaGetCode();
	$arResult['FORM']['CAPTCHA'] = array_key_exists('captcha_word', $postData) ? $postData['name'] : '';
}

$this->IncludeComponentTemplate();