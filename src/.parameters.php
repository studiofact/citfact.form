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
		'IBLOCK_ID' => array(
			'NAME' => Loc::getMessage('IBLOCK_ID'),
			'TYPE' => 'STRING',
			'DEFAULT' => '',
		),
		'CHECK_IBLOCK' => array(
			'NAME' => Loc::getMessage('CHECK_IBLOCK'),
			'TYPE' => 'CHECKBOX',
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
		'PHONE_REGEXP' => array(
			'NAME' => Loc::getMessage('PHONE_REGEXP'),
			'TYPE' => 'STRING',
			'DEFAULT' => '',
		),
		'REDIRECT_PATH' => array(
			'NAME' => Loc::getMessage('REDIRECT_PATH'),
			'TYPE' => 'STRING',
			'DEFAULT' => '',
		),
	)
);