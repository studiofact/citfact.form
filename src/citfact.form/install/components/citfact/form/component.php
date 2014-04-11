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
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

use Citfact\Form\HighLoadGenerator;
use Citfact\Form\HighLoadManager;

global $APPLICATION, $USER_FIELD_MANAGER;

Loc::loadMessages(__FILE__);

if (!Loader::includeModule('citfact.form')) {
    return ShowError('Module "citfact.form" not set');
}

if (!Loader::includeModule('highloadblock')) {
    return ShowError('Module "highloadblock" not set');
}

$application = Application::getInstance();
$applicationOld = & $APPLICATION;

$request = $application->getContext()->getRequest();
$componentId = CAjax::GetComponentID($this->getName(), $this->getTemplateName(), array());

$highLoadGenerator = new HighLoadGenerator($USER_FIELD_MANAGER);
$highLoadManager = new HighLoadManager($highLoadGenerator);

$highLoadGenerator->setHigeLoadBlockId($arParams['HLBLOCK_ID']);
if (!$highLoadGenerator->initHigeLoadBlock()) {
    return ShowError(sprintf('Highloadblock with ID = %d not found', $arParams['HLBLOCK_ID']));
}

// Validatation data in a form
if ($request->isPost() && $request->getPost('component_id') == $componentId) {
    $postData = array_map('strip_tags', $request->getPostList()->toArray());
    $highLoadManager->checkValueSelected($postData);

    if ($arParams['USE_CAPTCHA'] == 'Y') {
        if (!$applicationOld->CaptchaCheckCode($postData['captcha_word'], $postData['captcha_sid'])) {
            $errorList['captcha_word'] = Loc::getMessage('ERROR_CAPTCHA');
        }
    }

    $postData = array_intersect_key($postData, $highLoadGenerator->getHigeLoadBlockFields());
    $USER_FIELD_MANAGER->EditFormAddFields(sprintf('HLBLOCK_%d', $arParams['HLBLOCK_ID']), $postData);

    if (!$USER_FIELD_MANAGER->CheckFields(sprintf('HLBLOCK_%d', $arParams['HLBLOCK_ID']), null, $postData)) {
        $errorList['internal'] = $applicationOld->GetException();
    }

    if (!is_array($errorList)) {
        foreach (GetModuleEvents('citfact.form', 'onBeforeHighElementAdd', true) as $event) {
            ExecuteModuleEventEx($event, array(&$postData, $highLoadGenerator->getHigeLoadBlockData()));
        }

        $enityBase = $highLoadGenerator->getCompileBlock();
        $result = $enityBase::add($postData);

        if (!$result->isSuccess()) {
            $errorList = $result->getErrorMessages();
        } else {
            foreach (GetModuleEvents('citfact.form', 'onAfterHighElementAdd', true) as $event) {
                ExecuteModuleEventEx($event, array($result->getId(), &$postData, $highLoadGenerator->getHigeLoadBlockData()));
            }

            $highLoadManager->addEmailEvent($arParams['EVENT_NAME'], $arParams['EVENT_TEMPLATE'], $postData);
            $arResult['SUCCESS'] = true;
        }

        if ($result->isSuccess() && $arParams['AJAX'] != 'Y') {
            if (strlen($arParams['REDIRECT_PATH']) > 0) {
                LocalRedirect($arParams['REDIRECT_PATH']);
            }

            $redirectPath = $application
                ->getContext()
                ->getServer()
                ->getRequestUri();

            $_SESSION[sprintf('form_success_%s', $componentId)] = true;
            LocalRedirect($redirectPath);
        }
    }
}

// If after adding a redirect occurred at the same page
if (array_key_exists(sprintf('form_success_%s', $componentId), $_SESSION)) {
    unset($_SESSION[sprintf('form_success_%s', $componentId)]);
    $arResult['SUCCESS'] = true;
}

$postData = array_map('htmlspecialchars', (isset($postData)) ? $postData : array());
$errorList = (isset($errorList)) ? $highLoadManager->parseErrorList($errorList) : array();

$arResult = array_merge(array(
    'IS_POST' => $request->isPost(),
    'IS_AJAX' => (getenv('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest'),
    'SUCCESS' => false,
    'ERRORS' => $errorList,
    'HLBLOCK' => array(
        'DATA' => $highLoadGenerator->getHigeLoadBlockData(),
        'FIELDS' => $highLoadGenerator->getHigeLoadBlockFields(),
        'DISPLAY_FIELDS' => $highLoadManager->getDisplayFields((array)$arParams['DISPLAY_FIELDS'], (array)$arParams['TEXTAREA_FIELDS']),
    )
), $arResult);

$arResult['FORM']['COMPONENT_ID'] = $componentId;
foreach ($arResult['HLBLOCK']['DISPLAY_FIELDS'] as $name => $value) {
    $arResult['FORM'][$name] = array_key_exists($name, $postData) ? $postData[$name] : '';
}

if ($arParams['USE_CAPTCHA'] == 'Y') {
    $arResult['CAPTCHA_CODE'] = $applicationOld->CaptchaGetCode();
    $arResult['FORM']['CAPTCHA'] = array_key_exists('captcha_word', $postData) ? $postData['captcha_word'] : '';
}

// If enabled ajax mod check that it belongs to the current component
// If yes then look at the return type
if ($arResult['IS_AJAX'] && $request->getPost('component_id') == $componentId) {
    ob_start();
    $this->IncludeComponentTemplate();
    $componentTemplate = ob_get_contents();
    ob_end_clean();

    if (strtolower(LANG_CHARSET) != 'utf-8') {
        $componentTemplate = iconv(LANG_CHARSET, 'utf-8', $componentTemplate);
        foreach ($arResult['ERRORS'] as $key => $error) {
            $arResult['ERRORS'][$key] = iconv(LANG_CHARSET, 'utf-8', $error);
        }
    }

    $response = array(
        'success' => $arResult['SUCCESS'],
        'errors' => $arResult['ERRORS'],
        'redirect_path' => $arParams['REDIRECT_PATH'],
        'captcha' => $arResult['CAPTCHA_CODE'],
        'html' => $componentTemplate,
    );

    $applicationOld->RestartBuffer();
    header('Content-Type: application/json');
    exit(json_encode($response));
}

$this->IncludeComponentTemplate();