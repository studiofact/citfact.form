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

require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/classes/general/captcha.php';

Loader::includeModule('iblock');
Loader::includeModule('highloadblock');

Loader::registerAutoLoadClasses('citfact.form', array(
    'Citfact\Form\Attach\AbstractAttach' => 'lib/Citfact/Form/Attach/AbstractAttach.php',
    'Citfact\Form\Attach\AttachInterface' => 'lib/Citfact/Form/Attach/AttachInterface.php',
    'Citfact\Form\Attach\IBlockAttach' => 'lib/Citfact/Form/Attach/IBlockAttach.php',
    'Citfact\Form\Attach\UserFieldAttach' => 'lib/Citfact/Form/Attach/UserFieldAttach.php',
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
    'Citfact\Form\View\Type\IBlock\BaseIBlockType' => 'lib/Citfact/Form/View/Type/IBlock/BaseIBlockType.php',
    'Citfact\Form\View\Type\IBlock\CheckboxType' => 'lib/Citfact/Form/View/Type/IBlock/CheckboxType.php',
    'Citfact\Form\View\Type\IBlock\DateType' => 'lib/Citfact/Form/View/Type/IBlock/DateType.php',
    'Citfact\Form\View\Type\IBlock\FileType' => 'lib/Citfact/Form/View/Type/IBlock/FileType.php',
    'Citfact\Form\View\Type\IBlock\InputType' => 'lib/Citfact/Form/View/Type/IBlock/InputType.php',
    'Citfact\Form\View\Type\IBlock\RadioType' => 'lib/Citfact/Form/View/Type/IBlock/RadioType.php',
    'Citfact\Form\View\Type\IBlock\SelectType' => 'lib/Citfact/Form/View/Type/IBlock/SelectType.php',
    'Citfact\Form\View\Type\IBlock\TextareaType' => 'lib/Citfact/Form/View/Type/IBlock/TextareaType.php',
    'Citfact\Form\View\Type\UserField\BaseUserFieldType' => 'lib/Citfact/Form/View/Type/UserField/BaseUserFieldType.php',
    'Citfact\Form\View\Type\UserField\CheckboxType' => 'lib/Citfact/Form/View/Type/UserField/CheckboxType.php',
    'Citfact\Form\View\Type\UserField\DateType' => 'lib/Citfact/Form/View/Type/UserField/DateType.php',
    'Citfact\Form\View\Type\UserField\FileType' => 'lib/Citfact/Form/View/Type/UserField/FileType.php',
    'Citfact\Form\View\Type\UserField\InputType' => 'lib/Citfact/Form/View/Type/UserField/InputType.php',
    'Citfact\Form\View\Type\UserField\RadioType' => 'lib/Citfact/Form/View/Type/UserField/RadioType.php',
    'Citfact\Form\View\Type\UserField\SelectType' => 'lib/Citfact/Form/View/Type/UserField/SelectType.php',
    'Citfact\Form\View\Type\AbstractType' => 'lib/Citfact/Form/View/Type/AbstractType.php',
    'Citfact\Form\View\Type\BaseType' => 'lib/Citfact/Form/View/Type/BaseType.php',
    'Citfact\Form\View\Type\TypeInterface' => 'lib/Citfact/Form/View/Type/TypeInterface.php',
    'Citfact\Form\View\IBlockView' => 'lib/Citfact/Form/View/IBlockView.php',
    'Citfact\Form\View\UserFieldView' => 'lib/Citfact/Form/View/UserFieldView.php',
    'Citfact\Form\Event' => 'lib/Citfact/Form/Event.php',
    'Citfact\Form\EventResult' => 'lib/Citfact/Form/EventResult.php',
    'Citfact\Form\Form' => 'lib/Citfact/Form/Form.php',
    'Citfact\Form\FormBuilder' => 'lib/Citfact/Form/FormBuilder.php',
    'Citfact\Form\FormBuilderInterface' => 'lib/Citfact/Form/FormBuilderInterface.php',
    'Citfact\Form\FormEvents' => 'lib/Citfact/Form/FormEvents.php',
    'Citfact\Form\FormFactory' => 'lib/Citfact/Form/FormFactory.php',
    'Citfact\Form\FormValidator' => 'lib/Citfact/Form/FormValidator.php',
    'Citfact\Form\FormValidatorInterface' => 'lib/Citfact/Form/FormValidatorInterface.php',
    'Citfact\Form\FormView' => 'lib/Citfact/Form/FormView.php',
    'Citfact\Form\FormViewInterface' => 'lib/Citfact/Form/FormViewInterface.php',
    'Citfact\Form\Mailer' => 'lib/Citfact/Form/Mailer.php',
    'Citfact\Form\MailerBridge' => 'lib/Citfact/Form/MailerBridge.php',
    'Citfact\Form\MailerInterface' => 'lib/Citfact/Form/MailerInterface.php',
    'Citfact\Form\Storage' => 'lib/Citfact/Form/Storage.php',
    'Citfact\Form\StorageInterface' => 'lib/Citfact/Form/StorageInterface.php',
));
