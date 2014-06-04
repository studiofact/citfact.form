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
    protected $highLoadBlockId;

    /**
     * @var array
     */
    protected $highLoadBlockData;

    /**
     * @var array
     */
    protected $highLoadBlockFields;

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
     * Init highload block
     *
     * @return bool
     */
    public function initHighLoadBlock()
    {
        $cacheUniq = sprintf('hlblock_form_%d', $this->highLoadBlockId);
        if ($this->cacheProvider->initCache(3600, $cacheUniq)) {
            $cacheData = $this->cacheProvider->getVars();
            $this->setHighLoadBlockData($cacheData['hlblock']);
            $this->setHighLoadBlockFields($cacheData['hlblock_fields']);

            return true;
        }

        $highLoadBlock = HL\HighloadBlockTable::getById($this->highLoadBlockId)->fetch();
        if ($highLoadBlock && sizeof($highLoadBlock) > 0) {
            $highLoadBlockFields = $this->userFieldManager->GetUserFields(sprintf('HLBLOCK_%d', $highLoadBlock['ID']), 0, LANGUAGE_ID);

            $this->setHighLoadBlockData($highLoadBlock);
            $this->setHighLoadBlockFields($highLoadBlockFields);

            $this->setEnumValue();
            $this->setIblockElementValue();
            $this->setIblockSectionValue();

            $this->cacheProvider->startDataCache();
            $this->cacheProvider->endDataCache(array(
                'hlblock' => $highLoadBlock,
                'hlblock_fields' => $highLoadBlockFields
            ));
        }

        return (empty($highLoadBlock)) ? false : true;
    }

    /**
     * Set values for fields type enumeration
     *
     * @return void
     */
    private function setEnumValue()
    {
        $highLoadBLockFields = $this->getHighLoadBlockFields();
        $enumList = $enumValue = array();
        foreach ($highLoadBLockFields as $fieldName => $field) {
            if ($field['USER_TYPE_ID'] == 'enumeration') {
                $enumList[] = $field['ID'];
            }
        }

        if (!empty($enumList)) {
            $fieldEnum = \CUserFieldEnum::GetList(array(), array('USER_FIELD_ID' => $enumList));
            while ($row = $fieldEnum->GetNext()) {
                $row['SELECTED'] = 'N';
                $enumValue[$row['USER_FIELD_ID']][] = $row;
            }

            foreach ($highLoadBLockFields as $fieldName => $field) {
                if (array_key_exists($field['ID'], $enumValue)) {
                    $highLoadBLockFields[$fieldName]['VALUE'] = $enumValue[$field['ID']];
                }
            }
        }

        $this->setHighLoadBlockFields($highLoadBLockFields);
    }

    /**
     * Set values for fields type iblock_element
     *
     * @return void
     */
    private function setIblockElementValue()
    {
        $highLoadBLockFields = $this->getHighLoadBlockFields();
        $iblockList = $elementList = array();
        foreach ($highLoadBLockFields as $fieldName => $field) {
            if ($field['USER_TYPE_ID'] == 'iblock_element') {
                $iblockList[] = $field['SETTINGS']['IBLOCK_ID'];
            }
        }

        if (!empty($iblockList)) {
            $elementResult = \CIBlockElement::GetList(array(), array('IBLOCK_ID' => $iblockList), false, false, array('ID', 'IBLOCK_ID'));
            while ($row = $elementResult->GetNextElement()) {
                $fields = $row->GetFields();
                $fields['SELECTED'] = 'N';
                $elementList[$fields['IBLOCK_ID']][] = $fields;
            }

            foreach ($highLoadBLockFields as $fieldName => $field) {
                if (array_key_exists($field['SETTINGS']['IBLOCK_ID'], $elementList) && $field['USER_TYPE_ID'] == 'iblock_element') {
                    $highLoadBLockFields[$fieldName]['VALUE'] = $elementList[$field['SETTINGS']['IBLOCK_ID']];
                }
            }
        }

        $this->setHighLoadBlockFields($highLoadBLockFields);
    }

    /**
     * Set values for fields type iblock_section
     *
     * @return void
     */
    private function setIblockSectionValue()
    {
        $highLoadBLockFields = $this->getHighLoadBlockFields();
        $iblockList = $sectionList = array();
        foreach ($highLoadBLockFields as $fieldName => $field) {
            if ($field['USER_TYPE_ID'] == 'iblock_section') {
                $iblockList[] = $field['SETTINGS']['IBLOCK_ID'];
            }
        }

        if (!empty($iblockList)) {
            $sectionResult = \CIBlockSection::GetList(array(), array('IBLOCK_ID' => $iblockList), false, array(), false);
            while ($row = $sectionResult->GetNext()) {
                $row['SELECTED'] = 'N';
                $sectionList[$row['IBLOCK_ID']][] = $row;
            }

            foreach ($highLoadBLockFields as $fieldName => $field) {
                if (array_key_exists($field['SETTINGS']['IBLOCK_ID'], $sectionList) && $field['USER_TYPE_ID'] == 'iblock_section') {
                    $highLoadBLockFields[$fieldName]['VALUE'] = $sectionList[$field['SETTINGS']['IBLOCK_ID']];
                }
            }
        }

        $this->setHighLoadBlockFields($highLoadBLockFields);
    }

    /**
     * Return compiled class exteden DataManager
     *
     * @return mixed
     */
    public function getCompileBlock()
    {
        $enity = HL\HighloadBlockTable::compileEntity($this->getHighLoadBlockData());

        return $enity->getDataClass();
    }

    /**
     * Set highload block id
     *
     * @param int $id
     */
    public function setHighLoadBlockId($id)
    {
        $this->highLoadBlockId = (int)$id;
    }

    /**
     * Return highload block id
     *
     * @param int $id
     */
    public function getHighLoadBlockId()
    {
        return $this->highLoadBlockId;
    }

    /**
     * Set highload block data
     *
     * @param array $data
     */
    public function setHighLoadBlockData($data)
    {
        $this->highLoadBlockData = $data;
    }

    /**
     * Return highload block data
     *
     * @return array
     */
    public function getHighLoadBlockData()
    {
        return $this->highLoadBlockData;
    }

    /**
     * Set highload block fields
     *
     * @param array $fields
     */
    public function setHighLoadBlockFields($fields)
    {
        $this->highLoadBlockFields = $fields;
    }

    /**
     * Return highload block fields
     *
     * @return array
     */
    public function getHighLoadBlockFields()
    {
        return $this->highLoadBlockFields;
    }
}