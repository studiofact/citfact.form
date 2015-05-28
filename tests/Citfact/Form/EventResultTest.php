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

class EventResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EventResult
     */
    protected $eventResult = null;

    protected function setUp()
    {
        $this->eventResult = new EventResult();
    }

    public function testModifyFields()
    {
        $this->eventResult->modifyFields(array('modify'));
        $this->assertCount(1, $this->eventResult->getModified());
    }

    public function testUnsetFields()
    {
        $this->eventResult->unsetFields(array('unsetField1'));
        $this->assertCount(1, $this->eventResult->getUnset());

        $this->eventResult->unsetField('unsetField2');
        $this->assertCount(2, $this->eventResult->getUnset());
    }
}
