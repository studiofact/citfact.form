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

class RadioType extends BaseUserFieldType
{
    /**
     * @inheritdoc
     */
    public function detected()
    {
        if (in_array($this->field['USER_TYPE_ID'], array('iblock_element', 'iblock_section', 'enumeration'))) {
            if ($this->field['SETTINGS']['DISPLAY'] == 'CHECKBOX' && $this->field['MULTIPLE'] == 'N') {
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
        return 'radio';
    }
}
