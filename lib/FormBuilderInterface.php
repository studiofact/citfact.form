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

use Bitrix\Main\Type\ParameterDictionary;

interface FormBuilderInterface
{
    /**
     * Creates a form builder.
     *
     * @param ParameterDictionary $parameters
     * @return array
     */
    public function create(ParameterDictionary $parameters);
}