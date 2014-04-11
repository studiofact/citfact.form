<?php

/*
 * This file is part of the Studio Fact package.
 *
 * (c) Kulichkin Denis (onEXHovia) <onexhovia@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Bitrix\Main\Loader;

Loader::includeModule('iblock');

CModule::AddAutoloadClasses('citfact.form', array(
    'Citfact\Form\HighLoadGenerator' => 'lib/HighLoadGenerator.php',
    'Citfact\Form\HighLoadManager' => 'lib/HighLoadManager.php',
));

