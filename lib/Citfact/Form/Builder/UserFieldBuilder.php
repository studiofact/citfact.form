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
use Bitrix\Highloadblock as HL;
use Citfact\Form\Attach\AttachInterface;
use Citfact\Form\Attach\UserFieldAttach;
use Citfact\Form\FormViewInterface;
use Citfact\Form\Type\ParameterDictionary;
use Citfact\Form\Exception\BuilderException;
use Citfact\Form\View\UserFieldView;
use Citfact\Form\FormBuilder;

class UserFieldBuilder extends FormBuilder
{
    /**
     * @var array
     */
    protected $highLoadBlockFields;

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
        $highLoadBlockId = (int) $parameters->get('ID');
        $highLoadBlock = HL\HighloadBlockTable::getById($highLoadBlockId)->fetch();
        if (empty($highLoadBlock)) {
            throw new BuilderException(sprintf('Not found highloadblock with id = %d', $highLoadBlockId));
        }

        $highLoadBlockFields = $this->getUserFieldManager()
            ->GetUserFields(sprintf('HLBLOCK_%d', $highLoadBlockId), 0, LANGUAGE_ID);

        $this->setHighLoadBlockFields($highLoadBlockFields);
        $this->setElementValue();
        $this->setSectionValue();
        $this->setEnumValue();

        return array(
            'DATA' => $highLoadBlock,
            'FIELDS' => $this->getHighLoadBlockFields(),
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

        return $this->view = new UserFieldView($this);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttach()
    {
        if ($this->attach instanceof AttachInterface) {
            return $this->attach;
        }

        return $this->attach = new UserFieldAttach($this);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'userfields';
    }

    /**
     * Set values for fields type enumeration.
     */
    protected function setEnumValue()
    {
        $enumList = $this->getListByType('enumeration');
        $fieldEnum = $this->getUserFieldEnum()->getList(array(), array('USER_FIELD_ID' => $enumList));
        while ($row = $fieldEnum->fetch()) {
            foreach ($this->highLoadBlockFields as &$field) {
                if ($field['ID'] == $row['USER_FIELD_ID']) {
                    $field['VALUE_LIST'][] = $row;
                }
            }
        }
    }

    /**
     * Set values for fields type iblock_element.
     */
    protected function setElementValue()
    {
        $iblockList = $this->getListByType('iblock_element');
        $queryBuilder = new Entity\Query(Iblock\ElementTable::getEntity());
        $queryBuilder->setSelect(array('ID', 'NAME', 'IBLOCK_ID', 'IBLOCK_SECTION_ID'))
            ->setFilter(array('IBLOCK_ID' => $iblockList, 'ACTIVE' => 'Y'))
            ->setOrder(array());

        $elementResult = $queryBuilder->exec();
        while ($element = $elementResult->fetch()) {
            foreach ($this->highLoadBlockFields as &$field) {
                if ($field['USER_TYPE_ID'] != 'iblock_element') {
                    continue;
                }

                if ($field['SETTINGS']['IBLOCK_ID'] == $element['IBLOCK_ID']) {
                    $field['VALUE_LIST'][] = $element;
                }
            }
        }
    }

    /**
     * Set values for fields type iblock_section.
     */
    protected function setSectionValue()
    {
        $iblockList = $this->getListByType('iblock_section');
        $queryBuilder = new Entity\Query(Iblock\SectionTable::getEntity());
        $queryBuilder->setSelect(array('ID', 'NAME', 'IBLOCK_ID', 'IBLOCK_SECTION_ID', 'XML_ID'))
            ->setFilter(array('IBLOCK_ID' => $iblockList, 'ACTIVE' => 'Y'))
            ->setOrder(array());

        $sectionResult = $queryBuilder->exec();
        while ($section = $sectionResult->fetch()) {
            foreach ($this->highLoadBlockFields as &$field) {
                if ($field['USER_TYPE_ID'] != 'iblock_section') {
                    continue;
                }

                if ($field['SETTINGS']['IBLOCK_ID'] == $section['IBLOCK_ID']) {
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
        foreach ($this->highLoadBlockFields as $field) {
            if ($field['USER_TYPE_ID'] != $type) {
                continue;
            }

            switch ($type) {
                case 'iblock_section':
                case 'iblock_element':
                    $list[] = $field['SETTINGS']['IBLOCK_ID'];
                    break;

                case 'enumeration':
                    $list[] = $field['ID'];
            }
        }

        return $list;
    }

    /**
     * Set highload block fields.
     *
     * @param array $fields
     */
    public function setHighLoadBlockFields($fields)
    {
        $this->highLoadBlockFields = $fields;
    }

    /**
     * Return highload block fields.
     *
     * @return array
     */
    public function getHighLoadBlockFields()
    {
        return $this->highLoadBlockFields;
    }

    /**
     * @return \CUserTypeManager
     */
    protected function getUserFieldManager()
    {
        return new \CUserTypeManager();
    }

    /**
     * @return \CUserFieldEnum
     */
    protected function getUserFieldEnum()
    {
        return new \CUserFieldEnum();
    }
}
