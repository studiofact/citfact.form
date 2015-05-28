<?php

/*
 * This file is part of the Studio Fact package.
 *
 * (c) Kulichkin Denis (onEXHovia) <onexhovia@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// (bitrix|local)/modules/moduleName/tests
$_SERVER['DOCUMENT_ROOT'] = __DIR__.'/../../../..';

define('LANG', 'ru');
define('NO_KEEP_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS', true);
define('BX_BUFFER_USED', true);
define('BX_CLUSTER_GROUP', 2);

require $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php';
require __DIR__.'/../include.php';

$loader = require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
$loader->addPsr4('Citfact\\Form\\', __DIR__.'/Citfact/Form');
