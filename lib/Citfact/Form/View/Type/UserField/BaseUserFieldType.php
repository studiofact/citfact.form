<?php

/*
 * This file is part of the Studio Fact package.
 *
 * (c) Kulichkin Denis (onEXHovia) <onexhovia@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Citfact\Form\View\Type\UserField;

use Citfact\Form\View\Type\BaseType;

class BaseUserFieldType extends BaseType
{
    /**
     * @inheritdoc
     */
    public function getControlName()
    {
        return $this->field['FIELD_NAME'];
    }

    /**
     * @inheritdoc
     */
    public function getRequired()
    {
        return $this->field['MANDATORY'];
    }

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return $this->field['LIST_COLUMN_LABEL'];
    }

    /**
     * @inheritdoc
     */
    public function getDefaultValue()
    {
        return $this->field['SETTINGS']['DEFAULT_VALUE'] ?: '';
    }
}
