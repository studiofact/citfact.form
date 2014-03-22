<?php

/*
 * This file is part of the Studio Fact package.
 *
 * (c) Kulichkin Denis (onEXHovia) <onexhovia@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Citfact\Form;

use Bitrix\Highloadblock as HL;
use Bitrix\Main\Data\Cache;

class HighLoadGenerator
{
    /**
     * @var \CUserTypeManager
     */
    protected $userFieldManager;

    /**
     * @var \Bitrix\Main\Data\Cache
     */
    protected $cacheProvider;

    /**
     * @var int
     */
    protected $higeLoadBlockId;

    /**
     * @var array
     */
    protected $higeLoadBlockData;

    /**
     * @var array
     */
    protected $higeLoadBlockFields;

    /**
     * Construct object
     *
     * @param CUserTypeManager $userFieldManager
     */
    public function __construct($userFieldManager)
    {
        $this->userFieldManager = $userFieldManager;
        $this->cacheProvider = Cache::createInstance();
    }

    /**
     * Init higeload block
     *
     * @return bool
     */
    public function initHigeLoadBlock()
    {
        $cacheUniq = sprintf('hlblock_form_%d', $this->higeLoadBlockId);
        if ($this->cacheProvider->initCache(3600, $cacheUniq)) {
            $cacheData = $this->cacheProvider->getVars();
            $this->setHigeLoadBlockData($cacheData['hlblock']);
            $this->setHigeLoadBlockFields($cacheData['hlblock_fields']);

            return true;
        }

        $higeLoadBlock = HL\HighloadBlockTable::getById($this->higeLoadBlockId)->fetch();
        if ($higeLoadBlock && sizeof($higeLoadBlock) > 0) {
            $higeLoadBlockFields = $this->userFieldManager->GetUserFields(sprintf('HLBLOCK_%d', $higeLoadBlock['ID']), 0, LANGUAGE_ID);

            $this->setHigeLoadBlockData($higeLoadBlock);
            $this->setHigeLoadBlockFields($higeLoadBlockFields);

            $this->setEnumValue();
            $this->setIblockElementValue();
            $this->setIblockSectionValue();

            $this->cacheProvider->startDataCache();
            $this->cacheProvider->endDataCache(array(
                'hlblock' => $higeLoadBlock,
                'hlblock_fields' => $higeLoadBlockFields
            ));
        }

        return (empty($higeLoadBlock)) ? false : true;
    }

    /**
     * Set values for fields type enumeration
     *
     * @return void
     */
    private function setEnumValue()
    {
        $higeLoadBLockFields = $this->getHigeLoadBlockFields();
        $enumList = $enumValue = array();
        foreach ($higeLoadBLockFields as $fieldName => $field) {
            if ($field['USER_TYPE_ID'] == 'enumeration') {
                $enumList[] = $field['ID'];
            }
        }

        $fieldEnum = \CUserFieldEnum::GetList(array(), array('USER_FIELD_ID' => $enumList));
        while ($row = $fieldEnum->GetNext()) {
            $row['SELECTED'] = 'N';
            $enumValue[$row['USER_FIELD_ID']][] = $row;
        }

        foreach ($higeLoadBLockFields as $fieldName => $field) {
            if (array_key_exists($field['ID'], $enumValue)) {
                $higeLoadBLockFields[$fieldName]['VALUE'] = $enumValue[$field['ID']];
            }
        }

        $this->setHigeLoadBlockFields($higeLoadBLockFields);
    }

    /**
     * Set values for fields type iblock_element
     *
     * @return void
     */
    private function setIblockElementValue()
    {
        $higeLoadBLockFields = $this->getHigeLoadBlockFields();
        $iblockList = $elementList = array();
        foreach ($higeLoadBLockFields as $fieldName => $field) {
            if ($field['USER_TYPE_ID'] == 'iblock_element') {
                $iblockList[] = $field['SETTINGS']['IBLOCK_ID'];
            }
        }

        $elementResult = \CIBlockElement::GetList(array(), array('IBLOCK_ID' => $iblockList), false, false, array());
        while ($row = $elementResult->GetNext()) {
            $row['SELECTED'] = 'N';
            $elementList[$row['IBLOCK_ID']][] = $row;
        }

        foreach ($higeLoadBLockFields as $fieldName => $field) {
            if (array_key_exists($field['SETTINGS']['IBLOCK_ID'], $elementList) && $field['USER_TYPE_ID'] == 'iblock_element') {
                $higeLoadBLockFields[$fieldName]['VALUE'] = $elementList[$field['SETTINGS']['IBLOCK_ID']];
            }
        }

        $this->setHigeLoadBlockFields($higeLoadBLockFields);
    }

    /**
     * Set values for fields type iblock_section
     *
     * @return void
     */
    private function setIblockSectionValue()
    {
        $higeLoadBLockFields = $this->getHigeLoadBlockFields();
        $iblockList = $sectionList = array();
        foreach ($higeLoadBLockFields as $fieldName => $field) {
            if ($field['USER_TYPE_ID'] == 'iblock_section') {
                $iblockList[] = $field['SETTINGS']['IBLOCK_ID'];
            }
        }

        $sectionResult = \CIBlockSection::GetList(array(), array('IBLOCK_ID' => $iblockList), false, array(), false);
        while ($row = $sectionResult->GetNext()) {
            $row['SELECTED'] = 'N';
            $sectionList[$row['IBLOCK_ID']][] = $row;
        }

        foreach ($higeLoadBLockFields as $fieldName => $field) {
            if (array_key_exists($field['SETTINGS']['IBLOCK_ID'], $sectionList) && $field['USER_TYPE_ID'] == 'iblock_section') {
                $higeLoadBLockFields[$fieldName]['VALUE'] = $sectionList[$field['SETTINGS']['IBLOCK_ID']];
            }
        }

        $this->setHigeLoadBlockFields($higeLoadBLockFields);
    }

    /**
     * Return compiled class exteden DataManager
     *
     * @return mixed
     */
    public function getCompileBlock()
    {
        $enity = HL\HighloadBlockTable::compileEntity($this->getHigeLoadBlockData());

        return $enity->getDataClass();
    }

    /**
     * Set higeload block id
     *
     * @param int $id
     */
    public function setHigeLoadBlockId($id)
    {
        $this->higeLoadBlockId = (int)$id;
    }

    /**
     * Return higeload block id
     *
     * @param int $id
     */
    public function getHigeLoadBlockId()
    {
        return $this->higeLoadBlockId;
    }

    /**
     * Set higeload block data
     *
     * @param array $data
     */
    public function setHigeLoadBlockData($data)
    {
        $this->higeLoadBlockData = $data;
    }

    /**
     * Return higeload block data
     *
     * @return array
     */
    public function getHigeLoadBlockData()
    {
        return $this->higeLoadBlockData;
    }

    /**
     * Set higeload block fields
     *
     * @param array $fields
     */
    public function setHigeLoadBlockFields($fields)
    {
        $this->higeLoadBlockFields = $fields;
    }

    /**
     * Return higeload block fields
     *
     * @return array
     */
    public function getHigeLoadBlockFields()
    {
        return $this->higeLoadBlockFields;
    }
}