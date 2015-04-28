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

interface FormValidatorInterface
{
    /**
     * Return list errors after validate form
     *
     * @return array
     */
    public function getErrors();

    /**
     * Validate request
     *
     * @param Request $request
     * @param array $builderData
     */
    public function validate(Request $request, array $builderData);

}