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

$citfact_form_default_option = array(
    'BUILDER' => 'Citfact\\Form\\Builder\\UserFieldBuilder',
    'STORAGE' => 'Citfact\\Form\\Storage\\HighLoadBlockStorage',
    'VALIDATOR' => 'Citfact\\Form\\Validator\\UserFieldValidator',
);
