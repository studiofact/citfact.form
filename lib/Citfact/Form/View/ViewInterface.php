<?php

/*
 * This file is part of the Studio Fact package.
 *
 * (c) Kulichkin Denis (onEXHovia) <onexhovia@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Citfact\Form\View;

interface ViewInterface
{
    /**
     * Determines whether the field data type
     *
     * @param array $field
     * @param string $typeBuilder
     * @return bool
     */
    public function detected($field, $typeBuilder);

    /**
     * Return type name
     *
     * @return string
     */
    public function getName();
}