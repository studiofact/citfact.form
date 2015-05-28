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

use Citfact\Form\View\Type\TypeInterface;

interface FormViewInterface
{
    /**
     * @return TypeInterface[]
     */
    public function getDefaultViewTypes();

    /**
     * @param TypeInterface $type
     */
    public function addViewType(TypeInterface $type);

    /**
     * @return array
     */
    public function getViewData();

    /**
     * @param string $formName
     */
    public function setFormName($formName);

    /**
     * @param array $aliasFields
     */
    public function setAliasFields(array $aliasFields);

    /**
     * @param array $displayFields
     */
    public function setDisplayFields(array $displayFields);

    /**
     * @param array $request
     */
    public function setRequest(array $request);

    /**
     * @param array $errors
     */
    public function setErrors(array $errors);
}
