<?php

/*
 * This file is part of the Studio Fact package.
 *
 * (c) Kulichkin Denis (onEXHovia) <onexhovia@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Citfact\Form\View\Type;

interface TypeInterface
{
    /**
     * Determines whether the field data type.
     *
     * @return bool
     */
    public function detected();

    /**
     * @param array $field
     */
    public function setFieldData(array $field);

    /**
     * Return type name.
     *
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getMultiple();

    /**
     * @return string
     */
    public function getControlName();

    /**
     * @return string
     */
    public function getRequired();

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @return string
     */
    public function getDefaultValue();

    /**
     * @return array
     */
    public function getValueList();
}
