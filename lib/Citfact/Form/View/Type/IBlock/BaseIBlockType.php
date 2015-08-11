<?php

/*
 * This file is part of the Studio Fact package.
 *
 * (c) Kulichkin Denis (onEXHovia) <onexhovia@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Citfact\Form\View\Type\IBlock;

use Citfact\Form\View\Type\BaseType;

class BaseIBlockType extends BaseType
{
    /**
     * @inheritdoc
     */
    public function getControlName()
    {
        return $this->field['CODE'];
    }

    /**
     * @inheritdoc
     */
    public function getRequired()
    {
        return $this->field['IS_REQUIRED'];
    }

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return $this->field['NAME'];
    }

    /**
     * @inheritdoc
     */
    public function getDefaultValue()
    {
        return $this->field['DEFAULT_VALUE'];
    }
}
