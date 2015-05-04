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

interface FormBuilderInterface
{
    /**
     * Creates a form builder.
     *
     * @param ParameterDictionary $parameters
     * @return array
     */
    public function create(ParameterDictionary $parameters);

    /**
     * @return FormViewInterface
     */
    public function getView();

    /**
     * Return type builder
     *
     * @return string
     */
    public function getType();
}