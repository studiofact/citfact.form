<?php

/*
 * This file is part of the Studio Fact package.
 *
 * (c) Kulichkin Denis (onEXHovia) <onexhovia@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Citfact\Form\Storage;

use Citfact\Form\Validator\IBlockErrorParser;
use Citfact\Form\Storage as BaseStorage;

class IBlockStorage extends BaseStorage
{
    /**
     * @inheritdoc
     */
    public function save(array $request, array $builderData)
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

        $result = $iblockElement->Add($fields);
        if (is_numeric($result)) {
            return $result;
        }

        $iblockErrorParser = new IBlockErrorParser($builderData['FIELDS'], $builderData['DEFAULT_FIELDS']);
        $this->errorList = $iblockErrorParser->parse($iblockElement->LAST_ERROR);

        return false;
    }
}
