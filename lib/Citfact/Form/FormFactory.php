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
use Bitrix\Main\Config;

class FormFactory
{
    /**
     * @var ParameterDictionary
     */
    protected $params;

    /**
     * @param ParameterDictionary $params
     */
    public function __construct(ParameterDictionary $params)
    {
        $this->params = $params;
    }

    /**
     * @return Form
     */
    public function create()
    {
        if (!in_array($this->params->get('TYPE'), array('IBLOCK', 'HLBLOCK', 'CUSTOM'))) {
            $this->params->set('TYPE', 'CUSTOM');
        }

        $builder = Config\Option::get('citfact.form', 'BUILDER');
        $storage = Config\Option::get('citfact.form', 'STORAGE');
        $validator = Config\Option::get('citfact.form', 'VALIDATOR');

        switch ($this->params->get('TYPE')) {
            case 'IBLOCK':
                $builder = 'Citfact\\Form\\Builder\\IBlockBuilder';
                $storage = 'Citfact\\Form\\Storage\\IBlockStorage';
                $validator = 'Citfact\\Form\\Validator\\IBlockValidator';
                break;
            case 'HLBLOCK':
                $builder = 'Citfact\\Form\\Builder\\UserFieldBuilder';
                $storage = 'Citfact\\Form\\Storage\\HighLoadBlockStorage';
                $validator = 'Citfact\\Form\\Validator\\UserFieldValidator';
                break;
            case 'CUSTOM':
                $builder = $this->params->get('BUILDER') ?: $builder;
                $storage = $this->params->get('STORAGE') ?: $storage;
                $validator = $this->params->get('VALIDATOR') ?: $validator;
                break;
        }

        $builderInstance = new $builder();
        $validatorInstance = new $validator();
        $storageInstance = new $storage();

        $mailer = new MailerBridge(new Mailer($this->params, new \CEventType(), new \CEvent()), $builderInstance);
        $form = new Form($this->params, $builderInstance, $validatorInstance, $storageInstance);
        $form->setMailer($mailer);

        return $form;
    }
}
