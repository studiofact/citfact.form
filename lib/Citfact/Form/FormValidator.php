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
     * @param FormValidatorInterface $validator
     */
    public function __construct(FormValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param array $request
     * @param array $builderData
     */
    public function validate(array $request, array $builderData)
    {
        $this->validator->validate($request, $builderData);
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