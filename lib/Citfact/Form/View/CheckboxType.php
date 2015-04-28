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

class CheckboxType implements ViewInterface
{
    /**
     * @inheritdoc
     */
    public function detected($field, $typeBuilder)
    {
        if ($typeBuilder == 'userfields') {
            if (in_array($field['USER_TYPE_ID'], array('iblock_element', 'iblock_section', 'enumeration'))) {
                if ($field['SETTINGS']['DISPLAY'] == 'CHECKBOX' && $field['MULTIPLE'] == 'Y') {
                    return true;
                }
            }
        } elseif ($typeBuilder == 'iblock') {
            // For default fields
            if (isset($field['FIELD_TYPE']) && $field['FIELD_TYPE'] == 'checkbox') {
                return true;
            }

            if ($field['PROPERTY_TYPE'] == 'L' && $field['LIST_TYPE'] == 'C' && $field['MULTIPLE'] == 'Y') {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'checkbox';
    }
}