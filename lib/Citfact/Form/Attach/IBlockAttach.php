<?php

/*
 * This file is part of the Studio Fact package.
 *
 * (c) Kulichkin Denis (onEXHovia) <onexhovia@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Citfact\Form\Attach;

class IBlockAttach extends AbstractAttach
{
    /**
     * {@inheritdoc}
     */
    public function getFiles($insertId, array $attachFields)
    {
        $filesList = array();
        $iblockElement = new \CIBlockElement();
        $builderData = $this->builder->getBuilderData();

        $filter = array(
            'ID' => $insertId,
            'IBLOCK_ID' => $builderData['DATA']['ID'],
        );

        $elementDb = $iblockElement->getList(array(), $filter, false, false, array());
        if (!($element = $elementDb->getNextElement())) {
            return $filesList;
        }

        $fields = $element->getFields();
        foreach ($fields as $key => $value) {
            if (in_array($key, $attachFields) && is_numeric($value)) {
                $filesList[] = $value;
            }
        }

        $propertyList = $element->getProperties();
        foreach ($propertyList as $key => $property) {
            if (!in_array($key, $attachFields)) {
                continue;
            }

            if (is_numeric($property['VALUE'])) {
                $filesList[] = $property['VALUE'];
            } elseif (is_array($property['VALUE'])) {
                $filesList = array_merge_recursive($filesList, $property['VALUE']);
            }
        }

        return $filesList;
    }
}
