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
use Citfact\Form\Attach\AttachInterface;
use Citfact\Form\Attach\IBlockAttach;
use Citfact\Form\FormViewInterface;
use Citfact\Form\Type\ParameterDictionary;
use Citfact\Form\Exception\BuilderException;
use Citfact\Form\View\IBlockView;
use Citfact\Form\FormBuilder;

class IBlockBuilder extends FormBuilder
{
    /**
     * @var array
     */
    protected $iblockProperty;

    /**
     * @var FormViewInterface
     */
    protected $view;

    /**
     * @var AttachInterface
     */
    protected $attach;

    /**
     * @inheritdoc
     */
    public function create(ParameterDictionary $parameters)
    {
        $iblockId = (int) $parameters->get('ID');
        $queryBuilder = new Entity\Query(Iblock\IblockTable::getEntity());
        $iblockData = $queryBuilder->setSelect(array('ID', 'NAME'))
            ->setFilter(array('ID' => $iblockId))
            ->setOrder(array())
            ->exec()
            ->fetch();

        if (empty($iblockData)) {
            throw new BuilderException(sprintf('Not found iblock with id = %d', $iblockId));
        }

        // Get settings iblock
        $iblockDataFields = \CIBlock::GetArrayByID($iblockData['ID']);

        $queryBuilder = new Entity\Query(Iblock\PropertyTable::getEntity());
        $propertyResult = $queryBuilder->setSelect(array('*'))
            ->setFilter(array('IBLOCK_ID' => $iblockData['ID']))
            ->setOrder(array())
            ->exec();

        $propertyList = array();
        while ($property = $propertyResult->fetch()) {
            if (!empty($property['USER_TYPE_SETTINGS'])) {
                $property['USER_TYPE_SETTINGS'] = (($unserialize = @unserialize($property['USER_TYPE_SETTINGS'])) === false)
                    ? $property['USER_TYPE_SETTINGS']
                    : $unserialize;
            }

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
     * {@inheritdoc}
     */
    public function getView()
    {
        if ($this->view instanceof FormViewInterface) {
            return $this->view;
        }

        return $this->view = new IBlockView($this);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttach()
    {
        if ($this->attach instanceof AttachInterface) {
            return $this->attach;
        }

        return $this->attach = new IBlockAttach($this);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'iblock';
    }

    /**
     * @param array $settings
     * @param array $sectionValue
     *
     * @return array
     */
    protected function getDefaultFields($settings, $sectionValue)
    {
        return array(
            'ACTIVE' => array(
                'NAME' => $settings['FIELDS']['ACTIVE']['NAME'],
                'CODE' => 'ACTIVE',
                'FIELD_TYPE' => 'checkbox',
                'IS_REQUIRED' => $settings['FIELDS']['ACTIVE']['IS_REQUIRED'],
                'DEFAULT_VALUE' => $settings['FIELDS']['ACTIVE']['DEFAULT_VALUE'],
                'MULTIPLE' => 'N',
                'VALUE_LIST' => array(array(
                    'ID' => 'Y',
                    'NAME' => '',
                )),
            ),
            'ACTIVE_FROM' => array(
                'NAME' => $settings['FIELDS']['ACTIVE_FROM']['NAME'],
                'CODE' => 'ACTIVE_FROM',
                'FIELD_TYPE' => 'date',
                'IS_REQUIRED' => $settings['FIELDS']['ACTIVE_FROM']['IS_REQUIRED'],
                'DEFAULT_VALUE' => $settings['FIELDS']['ACTIVE_FROM']['DEFAULT_VALUE'],
                'MULTIPLE' => 'N',
            ),
            'ACTIVE_TO' => array(
                'NAME' => $settings['FIELDS']['ACTIVE_TO']['NAME'],
                'CODE' => 'ACTIVE_TO',
                'FIELD_TYPE' => 'date',
                'IS_REQUIRED' => $settings['FIELDS']['ACTIVE_TO']['IS_REQUIRED'],
                'DEFAULT_VALUE' => $settings['FIELDS']['ACTIVE_TO']['DEFAULT_VALUE'],
                'MULTIPLE' => 'N',
            ),
            'NAME' => array(
                'NAME' => $settings['FIELDS']['NAME']['NAME'],
                'CODE' => 'NAME',
                'FIELD_TYPE' => 'string',
                'IS_REQUIRED' => $settings['FIELDS']['NAME']['IS_REQUIRED'],
                'DEFAULT_VALUE' => $settings['FIELDS']['NAME']['DEFAULT_VALUE'],
                'MULTIPLE' => 'N',
            ),
            'CODE' => array(
                'NAME' => $settings['FIELDS']['CODE']['NAME'],
                'CODE' => 'CODE',
                'FIELD_TYPE' => 'string',
                'IS_REQUIRED' => $settings['FIELDS']['CODE']['IS_REQUIRED'],
                'DEFAULT_VALUE' => '',
                'MULTIPLE' => 'N',
            ),
            'SORT' => array(
                'NAME' => $settings['FIELDS']['SORT']['NAME'],
                'CODE' => 'SORT',
                'FIELD_TYPE' => 'string',
                'IS_REQUIRED' => $settings['FIELDS']['SORT']['IS_REQUIRED'],
                'DEFAULT_VALUE' => $settings['FIELDS']['SORT']['DEFAULT_VALUE'],
                'MULTIPLE' => 'N',
            ),
            'IBLOCK_SECTION' => array(
                'NAME' => $settings['FIELDS']['IBLOCK_SECTION']['NAME'],
                'CODE' => 'IBLOCK_SECTION',
                'FIELD_TYPE' => 'select',
                'IS_REQUIRED' => $settings['FIELDS']['IBLOCK_SECTION']['IS_REQUIRED'],
                'DEFAULT_VALUE' => '',
                'MULTIPLE' => 'Y',
                'VALUE_LIST' => $sectionValue,
            ),
            'PREVIEW_PICTURE' => array(
                'NAME' => $settings['FIELDS']['PREVIEW_PICTURE']['NAME'],
                'CODE' => 'PREVIEW_PICTURE',
                'FIELD_TYPE' => 'file',
                'IS_REQUIRED' => $settings['FIELDS']['PREVIEW_PICTURE']['IS_REQUIRED'],
                'DEFAULT_VALUE' => '',
                'MULTIPLE' => 'N',
            ),
            'DETAIL_PICTURE' => array(
                'NAME' => $settings['FIELDS']['DETAIL_PICTURE']['NAME'],
                'CODE' => 'DETAIL_PICTURE',
                'FIELD_TYPE' => 'file',
                'IS_REQUIRED' => $settings['FIELDS']['DETAIL_PICTURE']['IS_REQUIRED'],
                'DEFAULT_VALUE' => '',
                'MULTIPLE' => 'N',
            ),
            'PREVIEW_TEXT' => array(
                'NAME' => $settings['FIELDS']['PREVIEW_TEXT']['NAME'],
                'CODE' => 'PREVIEW_TEXT',
                'FIELD_TYPE' => 'textarea',
                'IS_REQUIRED' => $settings['FIELDS']['PREVIEW_TEXT']['IS_REQUIRED'],
                'DEFAULT_VALUE' => $settings['FIELDS']['PREVIEW_TEXT']['DEFAULT_VALUE'],
                'MULTIPLE' => 'N',
            ),
            'DETAIL_TEXT' => array(
                'NAME' => $settings['FIELDS']['DETAIL_TEXT']['NAME'],
                'CODE' => 'DETAIL_TEXT',
                'FIELD_TYPE' => 'textarea',
                'IS_REQUIRED' => $settings['FIELDS']['DETAIL_TEXT']['IS_REQUIRED'],
                'DEFAULT_VALUE' => $settings['FIELDS']['DETAIL_TEXT']['DEFAULT_VALUE'],
                'MULTIPLE' => 'N',
            ),
        );
    }

    /**
     * Set values for property with type L.
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
     * Set values for property with type E(link to element).
     */
    protected function setElementValue()
    {
        $iblockList = $this->getListByType('E');
        $queryBuilder = new Entity\Query(Iblock\ElementTable::getEntity());
        $queryBuilder->setSelect(array('ID', 'NAME', 'IBLOCK_ID', 'IBLOCK_SECTION_ID'))
            ->setFilter(array('IBLOCK_ID' => $iblockList, 'ACTIVE' => 'Y'))
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
     * Set values for property with type G(link to section).
     */
    protected function setSectionValue()
    {
        $iblockList = $this->getListByType('G');
        $queryBuilder = new Entity\Query(Iblock\SectionTable::getEntity());
        $queryBuilder->setSelect(array('ID', 'NAME', 'IBLOCK_ID', 'IBLOCK_SECTION_ID', 'XML_ID'))
            ->setFilter(array('IBLOCK_ID' => $iblockList, 'ACTIVE' => 'Y'))
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
     *
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
