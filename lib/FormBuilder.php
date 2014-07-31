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

class FormBuilder
{
    /**
     * @var FormBuilderInterface
     */
    protected $builder;

    /**
     * @var \Citfact\Form\Type\ParameterDictionary
     */
    protected $parameters;

    /**
     * @var array
     */
    protected $builderData = array();

    /**
     * @param FormBuilderInterface $builder
     * @param ParameterDictionary $parameters
     */
    public function __construct(FormBuilderInterface $builder, ParameterDictionary $parameters)
    {
        $this->builder = $builder;
        $this->parameters = $parameters;
    }

    /**
     * Build form data
     */
    public function create()
    {
        $builderData = $this->builder->create($this->parameters);

        $this->setBuilderData($builderData);
    }

    /**
     * @param array $builderData
     */
    public function setBuilderData($builderData)
    {
        $this->builderData = $builderData;
    }

    /**
     * @return array
     */
    public function getBuilderData()
    {
        return $this->builderData;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->builder->getType();
    }
}