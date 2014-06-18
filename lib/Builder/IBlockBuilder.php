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

        return array(
            'DATA' => $iblockData,
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