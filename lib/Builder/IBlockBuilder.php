<?php

/*
 * This file is part of the Studio Fact package.
 *
 * (c) Kulichkin Denis (onEXHovia) <onexhovia@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Citfact\Form\Builder;

use Bitrix\Iblock;
use Bitrix\Main\Entity;
use Citfact\Form\FormBuilderInterface;
use Citfact\Form\Type\ParameterDictionary;
use Citfact\Form\Exception\BuilderException;

class IBlockBuilder implements FormBuilderInterface
{
    /**
     * @var array
     */
    protected $iblockProperty;

    /**
     * @inheritdoc
     */
    public function create(ParameterDictionary $parameters)
    {
        $iblockId = (int)$parameters->get('ID');
        $queryBuilder = new Entity\Query(Iblock\IblockTable::getEntity());
        $iblockData = $queryBuilder->setSelect(array('ID', 'NAME'))
            ->setFilter(array('ID' => $iblockId))
            ->setOrder(array())
            ->exec()
            ->fetch();

        if (empty($iblockData)) {
            throw new BuilderException(sprintf('Not found iblock with id = %d', $iblockId));
        }

        $queryBuilder = new Entity\Query(Iblock\IblockFieldTable::getEntity());
        $iblockDataResult = $queryBuilder->setSelect(array('*'))
            ->setFilter(array('IBLOCK_ID' => $iblockData['ID']))
            ->setOrder(array())
            ->exec();

        $iblockDataFields = array();
        while ($field = $iblockDataResult->fetch()) {
            $iblockDataFields[$field['FIELD_ID']] = $field;
        }

        $queryBuilder = new Entity\Query(Iblock\PropertyTable::getEntity());
        $propertyResult = $queryBuilder->setSelect(array('*'))
            ->setFilter(array('IBLOCK_ID' => $iblockData['ID']))
            ->setOrder(array())
            ->exec();

        $propertyList = array();
        while ($property = $propertyResult->fetch()) {
            $propertyList[$property['CODE']] = $property;
        }

        $this->iblockProperty = $propertyList;
        $this->setElementValue();
        $this->setSectionValue();
        $this->setEnumValue();

        $sectionValueList = array();
        if (in_array($iblockData['ID'], $this->getListByType('G'))) {
            foreach ($this->iblockProperty as $field) {
                if ($field['PROPERTY_TYPE'] == 'G' && $field['LINK_IBLOCK_ID'] == $iblockData['ID']) {
                    $sectionValueList = (isset($field['VALUE_LIST'])) ? $field['VALUE_LIST'] : array();
                    break;
                }
            }
        } else {
            $queryBuilder = new Entity\Query(Iblock\SectionTable::getEntity());
            $sectionValueList = $queryBuilder->setSelect(array('ID', 'NAME'))
                ->setFilter(array('IBLOCK_ID' => $iblockData['ID']))
                ->setOrder(array())
                ->exec()
                ->fetchAll();
        }

        $upperLevel[] = array('ID' => 0, 'NAME' => GetMessage('IBLOCK_UPPER_LEVEL'));
        $sectionValueList = $upperLevel + $sectionValueList;

        return array(
            'DATA' => $iblockData,
            'DEFAULT_FIELDS' => $this->getDefaultFields($iblockDataFields, $sectionValueList),
            'FIELDS' => $this->iblockProperty,
        );
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return 'iblock';
    }

    /**
     * @param array $settings
     * @param array $sectionValue
     * @return array
     */
    protected function getDefaultFields($settings, $sectionValue)
    {
        return array(
            'ACTIVE' => array(
                'NAME' => GetMessage('IBLOCK_FIELD_ACTIVE'),
                'CODE' => 'ACTIVE',
                'FIELD_TYPE' => 'checkbox',
                'IS_REQUIRED' => $settings['ACTIVE']['IS_REQUIRED'],
                'MULTIPLE' => 'N',
                'VALUE_LIST' => array(array(
                    'ID' => 'Y',
                    'NAME' => ''
                )),
            ),
            'ACTIVE_FROM' => array(
                'NAME' => GetMessage('IBLOCK_FIELD_ACTIVE_FROM'),
                'CODE' => 'ACTIVE_FROM',
                'FIELD_TYPE' => 'date',
                'IS_REQUIRED' => $settings['ACTIVE_FROM']['IS_REQUIRED'],
                'MULTIPLE' => 'N',
            ),
            'ACTIVE_TO' => array(
                'NAME' => GetMessage('IBLOCK_FIELD_ACTIVE_TO'),
                'CODE' => 'ACTIVE_TO',
                'FIELD_TYPE' => 'date',
                'IS_REQUIRED' => $settings['ACTIVE_TO']['IS_REQUIRED'],
                'MULTIPLE' => 'N',
            ),
            'NAME' => array(
                'NAME' => GetMessage('IBLOCK_FIELD_NAME'),
                'CODE' => 'NAME',
                'FIELD_TYPE' => 'string',
                'IS_REQUIRED' => $settings['NAME']['IS_REQUIRED'],
                'MULTIPLE' => 'N',
            ),
            'CODE' => array(
                'NAME' => GetMessage('IBLOCK_FIELD_CODE'),
                'CODE' => 'CODE',
                'FIELD_TYPE' => 'string',
                'IS_REQUIRED' => $settings['CODE']['IS_REQUIRED'],
                'MULTIPLE' => 'N',
            ),
            'SORT' => array(
                'NAME' => GetMessage('IBLOCK_FIELD_SORT'),
                'CODE' => 'SORT',
                'FIELD_TYPE' => 'string',
                'IS_REQUIRED' => $settings['SORT']['IS_REQUIRED'],
                'MULTIPLE' => 'N',
            ),
            'IBLOCK_SECTION_ID' => array(
                'NAME' => GetMessage('IBLOCK_FIELD_SECTION_ID'),
                'CODE' => 'IBLOCK_SECTION_ID',
                'FIELD_TYPE' => 'select',
                'IS_REQUIRED' => $settings['IBLOCK_SECTION_ID']['IS_REQUIRED'],
                'MULTIPLE' => 'Y',
                'VALUE_LIST' => $sectionValue,
            ),
            'PREVIEW_PICTURE' => array(
                'NAME' => GetMessage('IBLOCK_FIELD_PREVIEW_PICTURE'),
                'CODE' => 'PREVIEW_PICTURE',
                'FIELD_TYPE' => 'file',
                'IS_REQUIRED' => $settings['PREVIEW_PICTURE']['IS_REQUIRED'],
                'MULTIPLE' => 'N',
            ),
            'DETAIL_PICTURE' => array(
                'NAME' => GetMessage('IBLOCK_FIELD_DETAIL_PICTURE'),
                'CODE' => 'DETAIL_PICTURE',
                'FIELD_TYPE' => 'file',
                'IS_REQUIRED' => $settings['DETAIL_PICTURE']['IS_REQUIRED'],
                'MULTIPLE' => 'N',
            ),
            'PREVIEW_TEXT' => array(
                'NAME' => GetMessage('IBLOCK_FIELD_PREVIEW_TEXT'),
                'CODE' => 'PREVIEW_TEXT',
                'FIELD_TYPE' => 'textarea',
                'IS_REQUIRED' => $settings['PREVIEW_TEXT']['IS_REQUIRED'],
                'MULTIPLE' => 'N',
            ),
            'DETAIL_TEXT' => array(
                'NAME' => GetMessage('IBLOCK_FIELD_DETAIL_TEXT'),
                'CODE' => 'DETAIL_TEXT',
                'FIELD_TYPE' => 'textarea',
                'IS_REQUIRED' => $settings['DETAIL_TEXT']['IS_REQUIRED'],
                'MULTIPLE' => 'N',
            ),
        );
    }

    /**
     * Set values for property with type L
     *
     * @return void
     */
    protected function setEnumValue()
    {
        $enumList = $this->getListByType('L');
        $queryBuilder = new Entity\Query(Iblock\PropertyEnumerationTable::getEntity());
        $queryBuilder->setSelect(array('*'))
            ->setFilter(array('PROPERTY_ID' => $enumList))
            ->setOrder(array());

        $enumListResult = $queryBuilder->exec();
        while ($enum = $enumListResult->fetch()) {
            foreach ($this->iblockProperty as &$field) {
                if ($field['PROPERTY_TYPE'] != 'L') {
                    continue;
                }

                if ($field['ID'] == $enum['PROPERTY_ID']) {
                    $field['VALUE_LIST'][] = $enum;
                }
            }
        }
    }

    /**
     * Set values for property with type E(link to element)
     *
     * @return void
     */
    protected function setElementValue()
    {
        $iblockList = $this->getListByType('E');
        $queryBuilder = new Entity\Query(Iblock\ElementTable::getEntity());
        $queryBuilder->setSelect(array('ID', 'NAME', 'IBLOCK_ID', 'IBLOCK_SECTION_ID'))
            ->setFilter(array('IBLOCK_ID' => $iblockList))
            ->setOrder(array());

        $elementResult = $queryBuilder->exec();
        while ($element = $elementResult->fetch()) {
            foreach ($this->iblockProperty as &$field) {
                if ($field['PROPERTY_TYPE'] != 'E') {
                    continue;
                }

                if ($field['LINK_IBLOCK_ID'] == $element['IBLOCK_ID']) {
                    $field['VALUE_LIST'][] = $element;
                }
            }
        }
    }

    /**
     * Set values for property with type G(link to section)
     *
     * @return void
     */
    protected function setSectionValue()
    {
        $iblockList = $this->getListByType('G');
        $queryBuilder = new Entity\Query(Iblock\SectionTable::getEntity());
        $queryBuilder->setSelect(array('ID', 'NAME', 'IBLOCK_ID', 'IBLOCK_SECTION_ID', 'XML_ID'))
            ->setFilter(array('IBLOCK_ID' => $iblockList))
            ->setOrder(array());

        $sectionResult = $queryBuilder->exec();
        while ($section = $sectionResult->fetch()) {
            foreach ($this->iblockProperty as &$field) {
                if ($field['PROPERTY_TYPE'] != 'G') {
                    continue;
                }

                if ($field['LINK_IBLOCK_ID'] == $section['IBLOCK_ID']) {
                    $field['VALUE_LIST'][] = $section;
                }
            }
        }
    }

    /**
     * @param string $type
     * @return array
     */
    protected function getListByType($type)
    {
        $list = array();
        foreach ($this->iblockProperty as $field) {
            if ($field['PROPERTY_TYPE'] != $type) {
                continue;
            }

            switch ($type) {
                case 'G':
                case 'E':
                    $list[] = $field['LINK_IBLOCK_ID'];
                    break;

                case 'L':
                    $list[] = $field['ID'];
            }
        }

        return $list;
    }
}