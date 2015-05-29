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

class EventTest extends \PHPUnit_Framework_TestCase
{
    public function testMergeFields()
    {
        $fields = array('NAME' => 'NAME', 'CODE' => 'CODE', 'SORT' => 500);
        $event = new Event(FormEvents::BUILD, $fields);
        $eventResult = new EventResult();
        $eventResult->modifyFields(array('NAME' => 'NAME_CHANGE'));
        $eventResult->unsetFields(array('CODE'));
        $eventResult->unsetField('SORT');

        $event->addResult($eventResult);
        $fields = $event->mergeFields($fields);

        $this->assertArrayNotHasKey('CODE', $fields);
        $this->assertArrayNotHasKey('SORT', $fields);

        $this->assertArrayHasKey('NAME', $fields);
        $this->assertEquals('NAME_CHANGE', $fields['NAME']);
    }
}
