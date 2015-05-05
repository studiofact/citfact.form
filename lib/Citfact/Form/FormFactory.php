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
     * @param ParameterDictionary $params
     * @return Form
     */
    public static function create(ParameterDictionary $params)
    {
        if (!in_array($params->get('TYPE'), array('IBLOCK', 'HLBLOCK', 'CUSTOM'))) {
            $params->set('TYPE', 'CUSTOM');
        }

        switch ($params->get('TYPE')) {
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
                $builder = $params->get('BUILDER') ?: Config\Option::get('citfact.form', 'BUILDER');
                $storage = $params->get('STORAGE') ?: Config\Option::get('citfact.form', 'STORAGE');
                $validator = $params->get('VALIDATOR') ?: Config\Option::get('citfact.form', 'VALIDATOR');
                break;
        }

        $mailer = new Mailer($params, new \CEventType, new \CEvent);
        $form = new Form($params, new $builder, new $validator, new $storage);
        $form->setMailer($mailer);

        return $form;
    }
}