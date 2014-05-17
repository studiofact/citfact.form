<?php

/*
 * This file is part of the Studio Fact package.
 *
 * (c) Kulichkin Denis (onEXHovia) <onexhovia@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;

Loc::loadMessages(__FILE__);

$parentMenu = (Loader::includeModule('citfact.core')) ? 'global_menu_citfact' : 'global_menu_services';
$menuList[] = array(
    'parent_menu' => $parentMenu,
    'section' => 'hlblock_template',
    'sort' => 200,
    'text' => Loc::getMessage('HLBLOCK_TEMPLATE_TEXT'),
    'url' => 'hlblock_template.php',
    'icon' => 'hlblock_template_menu_icon',
    'page_icon' => 'hlblock_template_page_icon',
    'more_url' => array(),
    'items_id' => 'hlblock_template',
    'items' => array(),
);

return $menuList;