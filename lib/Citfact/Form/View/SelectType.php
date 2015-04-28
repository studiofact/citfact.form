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

class SelectType implements ViewInterface
{
    /**
     * @inheritdoc
     */
    public function detected($field, $typeBuilder)
    {
        if ($typeBuilder == 'userfields') {
            if (in_array($field['USER_TYPE_ID'], array('iblock_element', 'iblock_section', 'enumeration'))) {
                return true;
            }
        } elseif ($typeBuilder == 'iblock') {
            // For default fields
            if (isset($field['FIELD_TYPE']) && $field['FIELD_TYPE'] == 'select') {
                return true;
            }

            if (in_array($field['PROPERTY_TYPE'], array('E', 'G', 'L'))) {
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
        return 'select';
    }
}