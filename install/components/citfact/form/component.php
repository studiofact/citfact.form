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
use Citfact\Form\FormFactory;
use Citfact\Form\Type\ParameterDictionary;

Loader::includeModule('citfact.form');

$app = Application::getInstance();
$params = new ParameterDictionary($arParams);
$result = new ParameterDictionary();
$formFactory = new FormFactory($params);
$form = $formFactory->create();

// Builder saves data to reduce the number of requests
if ($this->startResultCache()) {
    $arResult['BUILDER_DATA'] = $form->createBuilderData()->getBuilderData();
    $this->endResultCache();
}

$form->setBuilderData($arResult['BUILDER_DATA']);
$form->handleRequest($app->getContext()->getRequest());
$insertId = '';

if ($form->isValid()) {
    $insertId = $form->save();

    // If the data is successfully preserved produce redirect
    if ($params->get('AJAX') != 'Y') {
        $redirectPath = $params->get('REDIRECT_PATH') ?: getenv('REQUEST_URI');
        LocalRedirect($redirectPath);
    }
}

$result->set('BUILDER', $form->getBuilderData());
$result->set('VIEW', $form->getViewData());
$result->set('SUCCESS', $form->isValid());
$result->set('ERRORS', $form->getErrors(false));
$result->set('CSRF', $form->getCsrfToken());
$result->set('CAPTCHA', $form->getCaptchaToken());
$result->set('FORM_NAME', $form->getFormName());
$result->set('IS_AJAX', (getenv('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest'));

if ($result->get('IS_AJAX') && $form->isSubmitted()) {
    $GLOBALS['APPLICATION']->restartBuffer();
    header('Content-Type: application/json');

    ob_start();
    $arResult = $result->toArray();
    $this->includeComponentTemplate();
    $bufferTemplate = ob_get_contents();
    ob_clean();

    $response = array(
        'success' => $result->get('SUCCESS'),
        'errors' => $result->get('ERRORS'),
        'captcha' => $result->get('CAPTCHA'),
        'html' => $bufferTemplate,
        'insert_id' => $insertId,
    );

    exit(json_encode($response));
}

$arResult = $result->toArray();
$this->includeComponentTemplate();
