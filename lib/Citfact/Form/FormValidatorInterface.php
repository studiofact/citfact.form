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

interface FormValidatorInterface
{
    /**
     * Return list errors after validate form.
     *
     * @return array
     */
    public function getErrors();

    /**
     * Validate request.
     *
     * @param array $request
     * @param array $builderData
     */
    public function validate(array $request, array $builderData);

    /**
     * @return bool
     */
    public function isValid();
}
