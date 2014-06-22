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

use Bitrix\Main\Request;
use Citfact\Form\FormValidatorInterface;

class IBlockValidator implements FormValidatorInterface
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
    public function validate(Request $request, array $builderData)
    {
        $iblockElement = new \CIBlockElement();
        $postRequest = $request->getPostList()->toArray();

        $fields['IBLOCK_ID'] = $builderData['DATA']['ID'];
        foreach ($builderData['FIELDS'] as $fieldName => $field) {
            $fields['PROPERTY_VALUES'][$fieldName] = $postRequest[$fieldName];
        }

        if (!$iblockElement->checkFields($fields)) {
            $iblockErrorParser = new IBlockErrorParser($builderData['FIELDS']);
            $this->errorList = $iblockErrorParser->parse($iblockElement->LAST_ERROR);
        }
    }
}