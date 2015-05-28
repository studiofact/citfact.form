<?php

/*
 * This file is part of the Studio Fact package.
 *
 * (c) Kulichkin Denis (onEXHovia) <onexhovia@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Citfact\Form\Type;

class ParameterDictionaryTest extends \PHPUnit_Framework_TestCase
{
    public function testSet()
    {
        $params = new ParameterDictionary(array());

        $params->set('string', 'value');
        $this->assertEquals('value', $params->get('string'));

        $params->set(100, 200);
        $this->assertEquals(200, $params->get(100));
    }
}
