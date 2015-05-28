<?php

/*
 * This file is part of the Studio Fact package.
 *
 * (c) Kulichkin Denis (onEXHovia) <onexhovia@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Citfact\Form;

use Bitrix\Main\Loader;

class ExistsModuleTest extends \PHPUnit_Framework_TestCase
{
    public function testExists()
    {
        $loader = new Loader();
        $this->assertTrue($loader->includeModule('iblock'));
        $this->assertTrue($loader->includeModule('highloadblock'));
    }
}
