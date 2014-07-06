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

use Bitrix\Main\Request;
use Citfact\Form\StorageInterface;
use Citfact\Form\Validator\IBlockErrorParser;

class IblockStorage implements StorageInterface
{
    /**
     * @var array
     */
    protected $errorList = array();

    /**
     * @inheritdoc
     */
    public function getErrors()
    {
        return $this->errorList;
    }

    /**
     * @inheritdoc
     */
    public function save(Request $request, array $builderData)
    {
        $iblockElement = new \CIBlockElement();
        $postRequest = $request->getPostList()->toArray();

        $fields['IBLOCK_ID'] = $builderData['DATA']['ID'];
        foreach ($builderData['DEFAULT_FIELDS'] as $fieldName => $field) {
            $fields[$fieldName] = $postRequest[$fieldName];
        }

        foreach ($builderData['FIELDS'] as $fieldName => $field) {
            $fields['PROPERTY_VALUES'][$fieldName] = $postRequest[$fieldName];
        }

        $result = $iblockElement->Add($fields);
        if (is_numeric($result)) {
            return $result;
        }

        $iblockErrorParser = new IBlockErrorParser($builderData['FIELDS']);
        $this->errorList = $iblockErrorParser->parse($iblockElement->LAST_ERROR);

        return false;
    }

}