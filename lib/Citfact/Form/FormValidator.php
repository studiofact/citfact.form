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

class FormValidator
{
    /**
     * @var FormValidatorInterface
     */
    protected $validator;

    /**
     * @var array
     */
    protected $request;

    /**
     * @var array
     */
    protected $builderData;

    /**
     * @param FormValidatorInterface $validator
     * @param array $request
     * @param array $builderData
     */
    public function __construct(FormValidatorInterface $validator, array $request, array $builderData)
    {
        $this->validator = $validator;
        $this->request = $request;
        $this->builderData = $builderData;
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        $this->validator->validate($this->request, $this->builderData);
    }

    /**
     * Valid request form?
     *
     * @return bool
     */
    public function isValid()
    {
        return (sizeof($this->getErrors()) > 0) ? false : true;
    }

    /**
     * @inheritdoc
     */
    public function getErrors()
    {
        return $this->validator->getErrors();
    }
}