<?php

/*
 * This file is part of the Studio Fact package.
 *
 * (c) Kulichkin Denis (onEXHovia) <onexhovia@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Citfact\Form\Validator;

use Citfact\Form\FormValidator;

class IBlockValidator extends FormValidator
{
    /**
     * @inheritdoc
     */
    public function validate(array $request, array $builderData)
    {
        $iblockElement = new \CIBlockElement();
        $fields['IBLOCK_ID'] = $builderData['DATA']['ID'];

        foreach ($builderData['DEFAULT_FIELDS'] as $fieldName => $field) {
            if (isset($request[$fieldName])) {
                $fields[$fieldName] = $request[$fieldName];
            }
        }

        foreach ($builderData['FIELDS'] as $fieldName => $field) {
            if (isset($request[$fieldName])) {
                $fields['PROPERTY_VALUES'][$fieldName] = $request[$fieldName];
            }
        }

        if (!$iblockElement->checkFields($fields)) {
            $iblockErrorParser = new IBlockErrorParser($builderData['FIELDS'], $builderData['DEFAULT_FIELDS']);
            $this->errorList = $iblockErrorParser->parse($iblockElement->LAST_ERROR);
        }
    }
}
