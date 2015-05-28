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

abstract class BaseType extends AbstractType
{
    /**
     * @var array
     */
    protected $field = array();

    /**
     * @param array $field
     */
    public function setFieldData(array $field)
    {
        $this->field = $field;
    }

    /**
     * @return array
     */
    public function getFieldData()
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getMultiple()
    {
        return $this->field['MULTIPLE'];
    }

    /**
     * @return array
     */
    public function getValueList()
    {
        $valueList = (isset($this->field['VALUE_LIST'])) ? $this->field['VALUE_LIST'] : array();
        foreach ($valueList as $key => $value) {
            if (!array_key_exists('VALUE', $value)) {
                $valueList[$key]['VALUE'] = $value['NAME'];
            }
        }

        return $valueList;
    }
}
