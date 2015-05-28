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
                $builder = $this->params->get('BUILDER') ?: Config\Option::get('citfact.form', 'BUILDER');
                $storage = $this->params->get('STORAGE') ?: Config\Option::get('citfact.form', 'STORAGE');
                $validator = $this->params->get('VALIDATOR') ?: Config\Option::get('citfact.form', 'VALIDATOR');
                break;
        }

        $mailer = new Mailer($this->params, new \CEventType(), new \CEvent());
        $form = new Form($this->params, new $builder(), new $validator(), new $storage());
        $form->setMailer($mailer);

        return $form;
    }
}
