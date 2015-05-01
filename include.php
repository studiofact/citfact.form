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

Loader::registerAutoLoadClasses('citfact.form', array(
    'Citfact\Form\Builder\IBlockBuilder' => 'lib/Citfact/Form/Builder/IBlockBuilder.php',
    'Citfact\Form\Builder\UserFieldBuilder' => 'lib/Citfact/Form/Builder/UserFieldBuilder.php',
    'Citfact\Form\Exception\ExceptionInterface' => 'lib/Citfact/Form/Exception/ExceptionInterface.php',
    'Citfact\Form\Exception\BuilderException' => 'lib/Citfact/Form/Exception/BuilderException.php',
    'Citfact\Form\Extension\CaptchaExtension' => 'lib/Citfact/Form/Extension/CaptchaExtension.php',
    'Citfact\Form\Extension\CsrfExtension' => 'lib/Citfact/Form/Extension/CsrfExtension.php',
    'Citfact\Form\Extension\IdentifierExtension' => 'lib/Citfact/Form/Extension/IdentifierExtension.php',
    'Citfact\Form\Storage\IBlockStorage' => 'lib/Citfact/Form/Storage/IBlockStorage.php',
    'Citfact\Form\Storage\HighLoadBlockStorage' => 'lib/Citfact/Form/Storage/HighLoadBlockStorage.php',
    'Citfact\Form\Type\ParameterDictionary' => 'lib/Citfact/Form/Type/ParameterDictionary.php',
    'Citfact\Form\Validator\IBlockValidator' => 'lib/Citfact/Form/Validator/IBlockValidator.php',
    'Citfact\Form\Validator\IBlockErrorParser' => 'lib/Citfact/Form/Validator/IBlockErrorParser.php',
    'Citfact\Form\Validator\UserFieldValidator' => 'lib/Citfact/Form/Validator/UserFieldValidator.php',
    'Citfact\Form\View\CheckboxType' => 'lib/Citfact/Form/View/CheckboxType.php',
    'Citfact\Form\View\DateType' => 'lib/Citfact/Form/View/DateType.php',
    'Citfact\Form\View\FileType' => 'lib/Citfact/Form/View/FileType.php',
    'Citfact\Form\View\InputType' => 'lib/Citfact/Form/View/InputType.php',
    'Citfact\Form\View\RadioType' => 'lib/Citfact/Form/View/RadioType.php',
    'Citfact\Form\View\SelectType' => 'lib/Citfact/Form/View/SelectType.php',
    'Citfact\Form\View\TextareaType' => 'lib/Citfact/Form/View/TextareaType.php',
    'Citfact\Form\View\ViewInterface' => 'lib/Citfact/Form/View/ViewInterface.php',
    'Citfact\Form\Event' => 'lib/Citfact/Form/Event.php',
    'Citfact\Form\EventResult' => 'lib/Citfact/Form/EventResult.php',
    'Citfact\Form\Form' => 'lib/Citfact/Form/Form.php',
    'Citfact\Form\FormBuilder' => 'lib/Citfact/Form/FormBuilder.php',
    'Citfact\Form\FormBuilderInterface' => 'lib/Citfact/Form/FormBuilderInterface.php',
    'Citfact\Form\FormEvents' => 'lib/Citfact/Form/FormEvents.php',
    'Citfact\Form\FormValidator' => 'lib/Citfact/Form/FormValidator.php',
    'Citfact\Form\FormValidatorInterface' => 'lib/Citfact/Form/FormValidatorInterface.php',
    'Citfact\Form\FormView' => 'lib/Citfact/Form/FormView.php',
    'Citfact\Form\Mailer' => 'lib/Citfact/Form/Mailer.php',
    'Citfact\Form\MailerInterface' => 'lib/Citfact/Form/MailerInterface.php',
    'Citfact\Form\Storage' => 'lib/Citfact/Form/Storage.php',
    'Citfact\Form\StorageInterface' => 'lib/Citfact/Form/StorageInterface.php',
));
