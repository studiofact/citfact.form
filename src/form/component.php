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

use Bitrix\Highloadblock as HL;
use Bitrix\Main\Application;
use Bitrix\Main\Config\ConfigurationException;
use Bitrix\Main\Entity;
use Bitrix\Main\Event;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;

global $APPLICATION, $USER_FIELD_MANAGER;

Loc::loadMessages(__FILE__);

if (!Loader::includeModule('highloadblock')) {
    throw new LoaderException(sprintf('Module "Highloadblock" not set'));
}

$application = Application::getInstance();
// Instance for old application
$applicationOld = & $APPLICATION;

$isAjax = (getenv('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest') ? : false;
$componentId = CAjax::GetComponentID($this->getName(), $this->getTemplateName());
$request = $application->getContext()->getRequest();
$componentAjax = false;

// If after adding a redirect occurred at the same page 
if (array_key_exists(sprintf('feedback_success_%s', $componentId), $_SESSION)) {
    unset($_SESSION[sprintf('feedback_success_%s', $componentId)]);
    $success = true;
}

// Parsing errors after working method CheckFields
$parseErrorLitst = function ($errorList) use ($entityBaseFields) {
    $result = array();
    if (array_key_exists('captcha_word', $errorList)) {
        $result['captcha_word'] = $errorList['captcha_word'];
        unset($errorList['captcha_word']);
    }

    $errorList = (array_key_exists('internal', $errorList))
        ? $errorList['internal']
        : explode('<br>', $errorList[0]);

    if (is_object($errorList)) {
        foreach ($errorList->messages as $error) {
            if (!array_key_exists($error['id'], $result)) {
                $result[$error['id']] = $error['text'];
            }
        }
    } else {
        foreach ($entityBaseFields as $name => $field) {
            foreach ($errorList as $key => $error) {
                if (preg_match('#' . $field['EDIT_FORM_LABEL'] . '#', $error) || $field['ERROR_MESSAGE'] == $error) {
                    if (!array_key_exists($name, $result)) {
                        $result[$name] = $error;
                    }
                }
            }
        }
    }

    return array_diff($result, array(null));
};

// Returns a list of custom field values
$getEnumValue = function ($entityBaseFields) {
    $enumList = $enumValue = array();
    foreach ($entityBaseFields as $fieldName => $field) {
        if ($field['USER_TYPE_ID'] == 'enumeration') {
            $enumList[] = $field['ID'];
        }
    }

    $fieldEnum = CUserFieldEnum::GetList(array(), array('USER_FIELD_ID' => $enumList));
    while ($row = $fieldEnum->GetNext()) {
        $enumValue[$row['USER_FIELD_ID']][] = $row;
    }

    foreach ($entityBaseFields as $fieldName => $field) {
        if (array_key_exists($field['ID'], $enumValue)) {
            $entityBaseFields[$fieldName]['VALUE'] = $enumValue[$field['ID']];
        }
    }

    return $entityBaseFields;
};

// Checks the list of custom field values ​​for activity
$enumValueSelected = function ($entityBaseFields, $postData) {
    foreach ($entityBaseFields as $fieldName => $field) {
        if ($field['USER_TYPE_ID'] != 'enumeration') {
            continue;
        }

        $formValue = (array_key_exists($fieldName, $postData)) ? $postData[$fieldName] : '';
        foreach ($field['VALUE'] as $key => $value) {
            $entityBaseFields[$fieldName]['VALUE'][$key]['SELECTED'] =
                (is_array($formValue))
                    ? (in_array($value['ID'], $formValue)) ? 'Y' : 'N'
                    : ($value['ID'] == $formValue) ? 'Y' : 'N';
        }
    }

    return $entityBaseFields;
};

// For the current component ajax request?
$componentAjax = function () use ($componentId, $request, $isAjax) {
    if (!$request->isPost() || !$request->getPost('ajax_id')) {
        return false;
    }

    return ($request->getPost('ajax_id') == $componentId && $isAjax);
};

// Return new code for captcha
if ($arParams['USE_CAPTCHA'] == 'Y' && $request->getPost('feedback_captcha_remote') && $componentAjax) {
    $applicationOld->RestartBuffer();
    header('Content-Type: application/json');
    exit(json_encode(array('captcha' => $applicationOld->CaptchaGetCode())));
}

// Checking highload block
$hlblock = HL\HighloadBlockTable::getById($arParams['HLBLOCK_ID'])->fetch();
if (empty($hlblock)) {
    throw new ConfigurationException(sprintf('Highloadblock with ID = %d not found', $arParams['HLBLOCK_ID']));
}

$entityBase = HL\HighloadBlockTable::compileEntity($hlblock);
$entityBaseFields = $getEnumValue($USER_FIELD_MANAGER->GetUserFields(sprintf('HLBLOCK_%d', $hlblock['ID']), 0, LANGUAGE_ID));

// Validatation data in a form
if ($request->isPost() && $request->getPost(sprintf('send_form_%s', $componentId))) {
    $postData = $request->getPostList()->toArray();
    $postData = array_map('strip_tags', $postData);

    if ($arParams['USE_CAPTCHA'] == 'Y') {
        if (!$applicationOld->CaptchaCheckCode($postData['captcha_word'], $postData['captcha_sid'])) {
            $errorList['captcha_word'] = Loc::getMessage('ERROR_CAPTCHA');
        }
    }

    $postData = array_intersect_key($postData, $entityBaseFields);
    $USER_FIELD_MANAGER->EditFormAddFields(sprintf('HLBLOCK_%d', $hlblock['ID']), $postData);

    if (!$USER_FIELD_MANAGER->CheckFields(sprintf('HLBLOCK_%d', $hlblock['ID']), null, $postData)) {
        $errorList['internal'] = $applicationOld->GetException();
    }

    if (isset($errorList)) {
        $errorList = $parseErrorLitst($errorList);
    }

    if (empty($errorList)) {
        $enityData = $entityBase->getDataClass();
        $result = $enityData::add($postData);

        $success = ($result->isSuccess()) ? true : false;
        $internal = ($success === false) ? true : false;

        if ($success) {
            // Adding a post event
            $event = $arParams['EVENT_NAME'];
            $eventTemplate = (is_numeric($arParams['EVENT_TEMPLATE'])) ? : '';

            $eventType = CEventType::GetList(array('EVENT_NAME' => $event))->GetNext();
            if ($event && is_array($eventType)) {
                CEvent::send($event, SITE_ID, $postData, 'Y', $eventTemplate);
            }
        } else {
            $errorList = $parseErrorLitst($result->getErrorMessages());
        }

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
}

$postData = array_map('htmlspecialchars', (isset($postData)) ? $postData : array());
$arResult = array(
    'IS_POST' => $request->isPost(),
    'IS_AJAX' => $isAjax,
    'SUCCESS' => $success ? : false,
    'INTERNAL' => $internal ? : false,
    'ERRORS' => $errorList ? : array(),
    'HLBLOCK' => array(
        'DATA' => $hlblock,
        'FIELDS' => $enumValueSelected($entityBaseFields, $postData),
    )
);

$arResult['FORM']['COMPONENT_ID'] = $componentId;
foreach ($entityBaseFields as $name => $value) {
    $arResult['FORM'][$name] = array_key_exists($name, $postData) ? $postData[$name] : '';
}

if ($arParams['USE_CAPTCHA'] == 'Y') {
    $arResult['CAPTCHA_CODE'] = $applicationOld->CaptchaGetCode();
    $arResult['FORM']['CAPTCHA'] = array_key_exists('captcha_word', $postData) ? $postData['name'] : '';
}

// If enabled ajax mod and action in the request feedback_remote
// Return the validation form in the format json
if ($arParams['AJAX'] == 'Y' && $request->getPost('feedback_remote') && $componentAjax) {
    if (strtolower(LANG_CHARSET) != 'utf-8' && isset($errorList)) {
        foreach ($errorList as $key => $error) {
            $errorList[$key] = iconv(LANG_CHARSET, 'utf-8', $error);
        }
    }
    
    ob_start();
    $this->IncludeComponentTemplate();
    $componentTemplate = ob_get_contents();
    ob_end_clean();
    
    if (strtolower(LANG_CHARSET) != 'utf-8') {
        $componentTemplate = iconv(LANG_CHARSET, 'utf-8', $componentTemplate);
    }
    
    $jsonResponse = array(
        'success' => $arResult['SUCCESS'],
        'errors' => $arResult['ERRORS'],
        'internal' => $arResult['INTERNAL'],
        'html' => $componentTemplate,
        'use_redirect' => (strlen($arParams['REDIRECT_PATH']) > 0) ? : false,
        'redirect_path' => $arParams['REDIRECT_PATH'],
    );

    if ($arParams['USE_CAPTCHA'] == 'Y') {
        $jsonResponse['captcha'] = $arResult['CAPTCHA_CODE'];
    }

    $applicationOld->RestartBuffer();
    header('Content-Type: application/json');
    exit(json_encode($jsonResponse));
}

$this->IncludeComponentTemplate();
