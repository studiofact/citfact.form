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

$arComponentParameters = array(
	'PARAMETERS' => array(
		'HLBLOCK_ID' => array(
			'NAME' => Loc::getMessage('HLBLOCK_ID'),
			'TYPE' => 'STRING',
			'DEFAULT' => '',
		),
		'EVENT_NAME' => array(
			'NAME' => Loc::getMessage('EVENT_NAME'),
			'TYPE' => 'STRING',
			'DEFAULT' => '',
		),
		'EVENT_TEMPLATE' => array(
			'NAME' => Loc::getMessage('EVENT_TEMPLATE'),
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