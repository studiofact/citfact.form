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

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

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
        'ID' => array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('ID'),
            'TYPE' => 'STRING',
        ),
        'TYPE' => array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('TYPE'),
            'TYPE' => 'LIST',
            'VALUES' => array(
                'IBLOCK' => Loc::getMessage('TYPE_IBLOCK'),
                'HLBLOCK' => Loc::getMessage('TYPE_HLBLOCK'),
                'CUSTOM' => Loc::getMessage('TYPE_CUSTOM'),
            ),
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
        'EVENT_TYPE' => array(
            'NAME' => Loc::getMessage('EVENT_TYPE'),
            'TYPE' => 'STRING',
            'DEFAULT' => '',
        ),
        'BUILDER' => array(
            'NAME' => Loc::getMessage('BUILDER'),
            'TYPE' => 'STRING',
            'DEFAULT' => '',
        ),
        'STORAGE' => array(
            'NAME' => Loc::getMessage('STORAGE'),
            'TYPE' => 'STRING',
            'DEFAULT' => '',
        ),
        'VALIDATOR' => array(
            'NAME' => Loc::getMessage('VALIDATOR'),
            'TYPE' => 'STRING',
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
