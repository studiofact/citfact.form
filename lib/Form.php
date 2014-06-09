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

use Citfact\Form\Extension;
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
     * @var FormBuilder
     */
    private $builder;


    private $request;

    /**
     * @param ParameterDictionary $params
     */
    public function __construct(ParameterDictionary $params)
    {
        $this->params = $params;
    }

    /**
     * Init form
     */
    public function buildForm()
    {
        $builderStrategy = $this->getServices('builder');
        $this->builder = new FormBuilder(new $builderStrategy, $this->params);
    }

    /**
     * @param $request
     */
    public function handleRequest($request)
    {
        $this->request = $request;
    }

    /**
     * Save request form in storage
     */
    public function save()
    {

    }

    /**
     * Add errors of this form
     *
     * @param mixed $error
     * @return $this
     */
    public function addError($error)
    {
        $this->errors[] = $error;

        return $this;
    }

    /**
     * Return errors of this form
     */
    public function getErrors()
    {

    }

    /**
     * @return bool
     */
    public function isValid()
    {
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

        throw new \InvalidArgumentException('Not found services '. $name);
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
                throw new \InvalidArgumentException('Bad services '. $services);
        }

        return $this;
    }
}