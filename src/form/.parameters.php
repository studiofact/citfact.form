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
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

global $USER_FIELD_MANAGER;

if (!Loader::includeModule('highloadblock')) {
    throw new LoaderException(sprintf('Module "Highloadblock" not set'));
}

$hlblockList = array();
$hlblockListResult = HL\HighloadBlockTable::getList(array('select' => array('ID', 'NAME')));
while ($item = $hlblockListResult->fetch()) {
    $hlblockList[$item['ID']] = sprintf('[%d] %s', $item['ID'], $item['NAME']);
}

$userFiledsDisplay = array();
if ((int)$arCurrentValues['HLBLOCK_ID'] > 0) {
    $userFields = $USER_FIELD_MANAGER->GetUserFields(sprintf('HLBLOCK_%d', $arCurrentValues['HLBLOCK_ID']), 0, LANGUAGE_ID);
    if (sizeof($userFields) > 0) {
        foreach ($userFields as $fieldName => $field) {
            $userFiledsDisplay[$fieldName] = sprintf('[%s] %s', $fieldName, $field['LIST_COLUMN_LABEL']);
        }
    }
}

$eventTypeList = array();
$eventType = CEventType::GetList(array('LID' => SITE_ID));
while ($item = $eventType->GetNext()) {
    $eventTypeList[$item['EVENT_NAME']] = sprintf('[%s] %s', $item['EVENT_NAME'], $item['NAME']);
}

$eventTemplateList = array();
if (strlen($arCurrentValues['EVENT_NAME']) > 0) {
    $eventTemplate = CEventMessage::GetList(($o = ''), ($b = ''), array('EVENT_NAME' => $arCurrentValues['EVENT_NAME']));
    while ($item = $eventTemplate->GetNext()) {
        $eventTemplateList[$item['ID']] = sprintf('[%d] %s', $item['ID'], $item['SUBJECT']);
    }
}

$arComponentParameters = array(
    'PARAMETERS' => array(
        'HLBLOCK_ID' => array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('HLBLOCK_ID'),
            'TYPE' => 'LIST',
            'ADDITIONAL_VALUES' => 'Y',
            'VALUES' => $hlblockList,
            'REFRESH' => 'Y',
        ),
        'DISPLAY_FIELDS' => array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('DISPLAY_FIELDS'),
            'TYPE' => 'LIST',
            'MULTIPLE' => 'Y',
            'ADDITIONAL_VALUES' => 'Y',
            'VALUES' => $userFiledsDisplay,
        ),
        'TEXTAREA_FIELDS' => array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('TEXTAREA_FIELDS'),
            'TYPE' => 'LIST',
            'MULTIPLE' => 'Y',
            'ADDITIONAL_VALUES' => 'Y',
            'VALUES' => $userFiledsDisplay,
        ),
        'EVENT_NAME' => array(
            'NAME' => Loc::getMessage('EVENT_NAME'),
            'TYPE' => 'LIST',
            'ADDITIONAL_VALUES' => 'Y',
            'VALUES' => $eventTypeList,
            'REFRESH' => 'Y',
            'DEFAULT' => '',
        ),
        'EVENT_TEMPLATE' => array(
            'NAME' => Loc::getMessage('EVENT_TEMPLATE'),
            'TYPE' => 'LIST',
            'VALUES' => $eventTemplateList,
            'ADDITIONAL_VALUES' => 'Y',
            'DEFAULT' => '',
        ),
        'AJAX' => array(
            'NAME' => Loc::getMessage('AJAX'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => '',
        ),
        'USE_CAPTCHA' => array(
            'NAME' => Loc::getMessage('USE_CAPTCHA'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => '',
        ),
        'REDIRECT_PATH' => array(
            'NAME' => Loc::getMessage('REDIRECT_PATH'),
            'TYPE' => 'STRING',
            'DEFAULT' => '',
        ),
    )
);
