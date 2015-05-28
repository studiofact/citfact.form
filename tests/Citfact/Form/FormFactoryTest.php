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

use Citfact\Form\Type\ParameterDictionary;

class FormFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getTypeForm
     */
    public function testTypeCreate($type)
    {
        $formFactory = new FormFactory(new ParameterDictionary(array('TYPE' => $type)));
        $form = $formFactory->create();
        $this->assertTrue($form->getBuilder() instanceof FormBuilderInterface);
        $this->assertTrue($form->getStorage() instanceof StorageInterface);
        $this->assertTrue($form->getValidator() instanceof FormValidatorInterface);
        $this->assertTrue($form->getMailer() instanceof MailerInterface);
    }

    /**
     * @return array
     */
    public function getTypeForm()
    {
        return array(
            array('IBLOCK'),
            array('HLBLOCK'),
            array('CUSTOM'),
        );
    }
}
