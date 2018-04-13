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

use Bitrix\Main\Request;
use Citfact\Form\Extension\CaptchaExtension;
use Citfact\Form\Extension\CsrfExtension;
use Citfact\Form\Extension\IdentifierExtension;
use Citfact\Form\Exception\ValidateException;
use Citfact\Form\Type\ParameterDictionary;

class Form
{
    /**
     * The errors of this form.
     *
     * @var array
     */
    private $errors = array();

    /**
     * Whether this form was submitted.
     *
     * @var bool
     */
    private $submitted = false;

    /**
     * @var FormBuilderInterface
     */
    private $builder;

    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @var FormValidatorInterface
     */
    private $validator;

    /**
     * @var \Bitrix\Main\Request
     */
    private $request;

    /**
     * @var Extension\CaptchaExtension
     */
    private $captcha;

    /**
     * @var Extension\CsrfExtension
     */
    private $csrf;

    /**
     * @var Extension\IdentifierExtension
     */
    private $identifier;

    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var ParameterDictionary
     */
    private $params;

    /**
     * @param ParameterDictionary     $params
     * @param FormBuilderInterface    $builder
     * @param FormValidatorInterface  $validator
     * @param StorageInterface        $storage
     */
    public function __construct(ParameterDictionary $params, FormBuilderInterface $builder, FormValidatorInterface $validator, StorageInterface $storage)
    {
        $this->params = $params;
        $this->builder = $builder;
        $this->validator = $validator;
        $this->storage = $storage;
        $this->captcha = new CaptchaExtension();

        $paramsHash = md5(serialize($this->params->toArray()));
        $this->csrf = new CsrfExtension($paramsHash);
        $this->identifier = new IdentifierExtension($paramsHash);
    }

    /**
     * @param MailerInterface $mailer
     *
     * @return $this
     */
    public function setMailer(MailerInterface $mailer)
    {
        $this->mailer = $mailer;

        return $this;
    }

    /**
     * @return MailerInterface
     */
    public function getMailer()
    {
        return $this->mailer;
    }

    /**
     * @return $this
     */
    public function createBuilderData()
    {
        $builderData = $this->builder->create($this->params);
        $event = new Event(FormEvents::BUILD, $builderData, $this->builder);
        $event->send();

        $builderData = $event->mergeFields($builderData);
        $this->setBuilderData($builderData);

        return $this;
    }

    /**
     * @param array $builderData
     */
    public function setBuilderData(array $builderData)
    {
        $this->builder->setBuilderData($builderData);
    }

    /**
     * @return array
     */
    public function getBuilderData()
    {
        return $this->builder->getBuilderData();
    }

    /**
     * Inspects the given request.
     *
     * @param \Bitrix\Main\Request $request
     *
     * @return $this
     */
    public function handleRequest(Request $request)
    {
        $this->request = $request;
        $requestData = $this->getRequestData();

        if (!empty($requestData)) {
            $this->submitted = true;
        }

        if ($this->submitted === false) {
            return $this;
        }

        $csrf = $requestData['CSRF'] ?: '';
        if ($this->params->get('USE_CSRF') == 'Y' && !$this->csrf->isCsrfTokenValid($csrf)) {
            $this->addError('CSRF', 'CSRF_NOT_VALID');
        }

        if ($this->params->get('USE_CAPTCHA') == 'Y') {
            $captchaResponse = $requestData['CAPTCHA'] ?: '';
            $captchaToken = $requestData['CAPTCHA_TOKEN'] ?: '';
            if (!$this->captcha->isCaptchaTokenValid($captchaResponse, $captchaToken)) {
                $this->addError('CAPTCHA', 'CAPTCHA_NOT_VALID');
            }
        }

        $this->validator->validate($this->getRequestData(), $this->getBuilderData());
        if (!$this->validator->isValid()) {
            $this->addError('VALIDATOR', $this->validator->getErrors());
        }

        return $this;
    }

    /**
     * Save request form in storage.
     *
     * @return int|bool
     *
     * @throws ValidateException When not valid request
     */
    public function save()
    {
        if ($this->isValid() === false) {
            throw new ValidateException('Request validation failed');
        }

        $requestData = $this->getRequestData();
        $event = new Event(FormEvents::PRE_STORAGE, $requestData, $this->builder);
        $event->send();

        $requestData = $event->mergeFields($requestData);
        $insertId = $this->storage->save($requestData, $this->getBuilderData());

        if (!$this->storage->isSuccess()) {
            $this->addError('STORAGE', $this->storage->getErrors());
        } else {
            $event = new Event(FormEvents::STORAGE, $requestData, $this->builder);
            $event->send();

            $requestData = $event->mergeFields($requestData);
            if ($this->mailer instanceof MailerInterface) {
                $attachFiles = array();
                // Attaching files is available from version 15.0.15
                if (version_compare(SM_VERSION, '15.0.15', '>=')) {
                    $attach = $this->builder->getAttach();
                    $attachFiles = $attach->getFiles($insertId, $this->params->get('ATTACH_FIELDS'));
                }

                if ($this->mailer instanceof MailerBridge) {
                    $this->mailer->setViewData($this->getViewData());
                }

                $macrosData = array_merge($requestData, array('INSERT_ID' => $insertId));
                $this->mailer->send($macrosData, $attachFiles);
            }
        }

        return $insertId;
    }

    /**
     * Add errors of this form.
     *
     * @param mixed $type
     * @param mixed $error
     *
     * @return $this
     */
    public function addError($type, $error)
    {
        $this->errors[$type] = $error;

        return $this;
    }

    /**
     * Return errors of this form.
     *
     * @param bool $original
     *
     * @return array
     */
    public function getErrors($original = true)
    {
        if ($original) {
            return $this->errors;
        }

        $errorsList = array('ORIGINAL' => null, 'LIST' => array());
        $errorsList['ORIGINAL'] = $this->errors;
        foreach ($this->errors as $type => $error) {
            if (!is_array($error)) {
                $errorsList['LIST'][$type] = $error;
            } else {
                foreach ($error as $key => $message) {
                    $errorsList['LIST'][$key] = $message;
                }
            }
        }

        return $errorsList;
    }

    /**
     * Returns whether the form are valid.
     *
     * @return bool
     */
    public function isValid()
    {
        if (!$this->isSubmitted()) {
            return false;
        }

        if (count($this->getErrors()) > 0) {
            return false;
        }

        return true;
    }

    /**
     * Returns whether the form is submitted.
     *
     * @return bool
     */
    public function isSubmitted()
    {
        return $this->submitted;
    }

    /**
     * Return request data in an array.
     *
     * @return array
     */
    public function getRequestData()
    {
        $postList = array();
        $formName = $this->getFormName();

        $requestData = $this->request->getPostList()->toArray();
        if (array_key_exists($formName, $requestData)) {
            $postList = array_merge($postList, $requestData[$formName]);
        }

        $filesData = $this->request->getFileList()->toArray();
        if (array_key_exists($formName, $filesData)) {
            $filesData = $filesData[$formName];
            $postList = array_merge($postList, $this->normalizeFilesData($filesData));
        }

        return $postList;
    }

    /**
     * @param array $filesData
     *
     * @return array
     */
    private function normalizeFilesData($filesData)
    {
        $filesResult = array();
        foreach ($filesData as $nameType => $valueData) {
            foreach ($valueData as $fieldName => $fieldValue) {
                // If property or field is not multiple
                if (!is_array($fieldValue)) {
                    $filesResult[$fieldName][$nameType] = $fieldValue;
                    continue;
                }

                foreach ($fieldValue as $key => $value) {
                    $filesResult[$fieldName][$key][$nameType] = $value;
                }
            }
        }

        return $filesResult;
    }

    /**
     * @return string
     */
    public function getFormName()
    {
        return sprintf('FORM_%d', $this->getIdentifierToken());
    }

    /**
     * @return array
     */
    public function getViewData()
    {
        $errors = $this->getErrors(false);
        $view = $this->builder->getView();

        $aliasFields = array_diff((array) $this->params->get('ALIAS_FIELDS'), array(null));
        $displayFields = array_diff((array) $this->params->get('DISPLAY_FIELDS'), array(null));

        return $view->setRequest($this->getRequestData())
            ->setErrors($errors['LIST'])
            ->setFormName($this->getFormName())
            ->setAliasFields($aliasFields)
            ->setDisplayFields($displayFields)
            ->create()
            ->getViewData();
    }

    /**
     * Return params component.
     *
     * @return ParameterDictionary
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Return builder of this form.
     *
     * @return FormBuilderInterface
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * @return StorageInterface
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * @return FormValidatorInterface
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * Return a CSRF token.
     *
     * @return string
     */
    public function getCsrfToken()
    {
        return $this->csrf->generateCsrfToken();
    }

    /**
     * Return a CAPTCHA token.
     *
     * @return string
     */
    public function getCaptchaToken()
    {
        return $this->captcha->generateCaptchaToken();
    }

    /**
     * Return form ID token.
     *
     * @return string
     */
    public function getIdentifierToken()
    {
        return $this->identifier->generateIdentifier();
    }
}
