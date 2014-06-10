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
     * The errors of this form
     *
     * @var array
     */
    private $errors = array();

    /**
     * Store register services
     *
     * @var array
     */
    private $services = array();

    /**
     * Whether this form was submitted
     *
     * @var bool
     */
    private $submitted = false;

    /**
     * @var \Citfact\Form\FormBuilder
     */
    private $builder;

    /**
     * @var \Bitrix\Main\Request;
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
     * @param ParameterDictionary $params
     */
    public function __construct(ParameterDictionary $params)
    {
        $this->params = $params;
        $this->captcha = new CaptchaExtension();
        $this->csrf = new CsrfExtension();
        $this->identifier = new IdentifierExtension();
    }

    /**
     * Get the data on which you can collect form
     *
     * @return $this
     */
    public function buildForm()
    {
        $builderStrategy = $this->getServices('builder');
        $this->builder = new FormBuilder(new $builderStrategy, $this->params);
        $this->builder->create();

        return $this;
    }

    /**
     * Inspects the given request
     *
     * @param \Bitrix\Main\Request $request
     * @return $this
     */
    public function handleRequest(Request $request)
    {
        $this->request = $request;
        $componentId = $this->request->getPost('COMPONENT_ID');
        if ($this->identifier->isIdentifierValid($componentId)) {
            $this->submitted = true;
        }

        if ($this->submitted === false) {
            return $this;
        }

        if (!$this->csrf->isCsrfTokenValid('', $this->request->getPost('CSRF'))) {
            $this->addError('CSRF', 'CSRF_NOT_VALID');
        }

        if ($this->params->get('USE_CAPTCHA') == 'Y') {
            $captchaResponse = $this->request->getPost('CAPTCHA');
            $captchaToken = $this->request->getPost('CAPTCHA_TOKEN');
            if (!$this->captcha->isCaptchaTokenValid($captchaResponse, $captchaToken)) {
                $this->addError('CAPTCHA', 'CAPTCHA_NOT_VALID');
            }
        }

        $validatorStrategy = $this->getServices('validator');
        $validator = new FormValidator(
            new $validatorStrategy,
            $this->getRequest(),
            $this->getBuilder()->getBuilderData()
        );

        $validator->validate();
        if (!$validator->isValid()) {
            $this->addError('VALIDATOR', $validator->getErrors());
        }

        return $this;
    }

    /**
     * Save request form in storage
     *
     * @return $this
     */
    public function save()
    {
        if ($this->isValid() === false) {
            throw new ValidateException('Request validation failed');
        }

        $storageStrategy = $this->getServices('storage');
        $storage = new Storage(
            new $storageStrategy,
            $this->getRequest(),
            $this->getBuilder()->getBuilderData()
        );

        $storage->save();
        if (!$storage->isSuccess()) {
            $this->addError('STORAGE', $storage->getErrors());
        }

        return $this;
    }

    /**
     * Add errors of this form
     *
     * @param mixed $type
     * @param mixed $error
     * @return $this
     */
    public function addError($type, $error)
    {
        $this->errors[$type] = $error;

        return $this;
    }

    /**
     * Return errors of this form
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Returns whether the form are valid.
     *
     * @return bool
     */
    public function isValid()
    {
        if (!$this->submitted) {
            return false;
        }

        if (count($this->getErrors()) > 0) {
            return false;
        }

        return true;
    }

    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return array
     */
    public function getRequestData()
    {
        return array();
    }

    /**
     * @return ParameterDictionary
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @return FormBuilder
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * Return a CSRF token
     *
     * @return string
     */
    public function getCsrfToken()
    {
        return $this->csrf->generateCsrfToken('');
    }

    /**
     * Return a CAPTCHA token
     *
     * @return string
     */
    public function getCaptchaToken()
    {
        return $this->captcha->generateCaptchaToken();
    }

    /**
     * Return form ID token
     *
     * @return string
     */
    public function getIdentifierToken()
    {
        return $this->identifier->generateIdentifier();
    }

    /**
     * Get services
     *
     * @param string $name
     * @return string
     * @throws \InvalidArgumentException When services not found
     */
    public function getServices($name)
    {
        if (array_key_exists($name, $this->services)) {
            return $this->services[$name];
        }

        throw new \InvalidArgumentException('Not found services ' . $name);
    }

    /**
     * Register services
     *
     * @param string $services
     * @param string $class
     * @return $this
     * @throws \InvalidArgumentException When invalid services
     */
    public function register($services, $class)
    {
        switch ($services) {
            case 'builder':
            case 'storage':
            case 'validator':
                $this->services[$services] = $class;
                break;

            default:
                throw new \InvalidArgumentException('Bad services ' . $services);
        }

        return $this;
    }
}