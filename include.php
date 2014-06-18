<?php

/*
 * This file is part of the Studio Fact package.
 *
 * (c) Kulichkin Denis (onEXHovia) <onexhovia@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;

Loader::includeModule('iblock');
Loader::includeModule('highloadblock');

CModule::AddAutoloadClasses('citfact.form', array(
    'Citfact\Form\Builder\IBlockBuilder' => 'lib/Builder/IBlockBuilder.php',
    'Citfact\Form\Builder\UserFieldBuilder' => 'lib/Builder/UserFieldBuilder.php',
    'Citfact\Form\Exception\ExceptionInterface' => 'lib/Exception/ExceptionInterface.php',
    'Citfact\Form\Exception\BuilderException' => 'lib/Exception/BuilderException.php',
    'Citfact\Form\Extension\CaptchaExtension' => 'lib/Extension/CaptchaExtension.php',
    'Citfact\Form\Extension\CsrfExtension' => 'lib/Extension/CsrfExtension.php',
    'Citfact\Form\Extension\IdentifierExtension' => 'lib/Extension/IdentifierExtension.php',
    'Citfact\Form\Storage\IBlockStorage' => 'lib/Storage/IBlockStorage.php',
    'Citfact\Form\Storage\HighLoadBlockStorage' => 'lib/Storage/HighLoadBlockStorage.php',
    'Citfact\Form\Type\ParameterDictionary' => 'lib/Type/ParameterDictionary.php',
    'Citfact\Form\Validator\IBlockValidator' => 'lib/Validator/IBlockValidator.php',
    'Citfact\Form\Validator\UserFieldValidator' => 'lib/Validator/UserFieldValidator.php',
    'Citfact\Form\View\CheckboxType' => 'lib/View/CheckboxType.php',
    'Citfact\Form\View\DateType' => 'lib/View/DateType.php',
    'Citfact\Form\View\FileType' => 'lib/View/FileType.php',
    'Citfact\Form\View\InputType' => 'lib/View/InputType.php',
    'Citfact\Form\View\RadioType' => 'lib/View/RadioType.php',
    'Citfact\Form\View\SelectType' => 'lib/View/SelectType.php',
    'Citfact\Form\View\TextareaType' => 'lib/View/TextareaType.php',
    'Citfact\Form\View\ViewInterface' => 'lib/View/ViewInterface.php',
    'Citfact\Form\Event' => 'lib/Event.php',
    'Citfact\Form\Form' => 'lib/Form.php',
    'Citfact\Form\FormBuilder' => 'lib/FormBuilder.php',
    'Citfact\Form\FormBuilderInterface' => 'lib/FormBuilderInterface.php',
    'Citfact\Form\FormValidator' => 'lib/FormValidator.php',
    'Citfact\Form\FormValidatorInterface' => 'lib/FormValidatorInterface.php',
    'Citfact\Form\FormView' => 'lib/FormView.php',
    'Citfact\Form\Mailer' => 'lib/Mailer.php',
    'Citfact\Form\Storage' => 'lib/Storage.php',
    'Citfact\Form\StorageInterface' => 'lib/StorageInterface.php',
));
