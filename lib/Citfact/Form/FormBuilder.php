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

abstract class FormBuilder implements FormBuilderInterface
{
    /**
     * @var array
     */
    protected $builderData = array();

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
}