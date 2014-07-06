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

use Citfact\Form\View;
use Citfact\Form\View\ViewInterface;
use Citfact\Form\Type\ParameterDictionary;

class FormView
{
    private $viewType = array();
    private $viewData = array();
    private $formBuilder;
    private $parameters;

    /**
     * @param FormBuilder $formBuilder
     */
    public function __construct(FormBuilder $formBuilder, ParameterDictionary $parameters)
    {
        $this->formBuilder = $formBuilder;
        $this->parameters = $parameters;
        $this->addViewType(new View\InputType());
        $this->addViewType(new View\TextareaType());
        $this->addViewType(new View\DateType());
        $this->addViewType(new View\FileType());
        $this->addViewType(new View\SelectType());
        $this->addViewType(new View\CheckboxType());
        $this->addViewType(new View\RadioType());
    }

    /**
     * @param ViewInterface $type
     * @return $this
     */
    public function addViewType(ViewInterface $type)
    {
        if (!isset($this->viewType[$type->getName()])) {
            $this->viewType[$type->getName()] = $type;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function create()
    {
        $builderData = $this->formBuilder->getBuilderData();
        $builderType = $this->formBuilder->getType();
        if ($builderType == 'iblock') {
            $builderData['FIELDS'] = $builderData['DEFAULT_FIELDS'] + $builderData['FIELDS'];
        }

        foreach ($builderData['FIELDS'] as $fieldName => $field) {
            foreach ($this->viewType as $type) {
                if (!$type->detected($field, $builderType)) {
                    continue;
                }

                $this->viewData[$fieldName] = array(
                    'TYPE' => $type->getName(),
                    'REQUIRED' => $this->getRequired($field),
                    'MULTIPLE' => $field['MULTIPLE'],
                    'NAME' => $this->getControlName($field),
                    'LABEL' => $this->getLabel($field),
                    'VALUE_LIST' => $this->getValue($field),
                );
            }
        }

        return $this;
    }

    /**
     * @param array $field
     * @return string
     */
    protected function getRequired($field)
    {
        return ($this->formBuilder->getType() == 'iblock') ? $field['IS_REQUIRED'] : $field['MANDATORY'];
    }

    /**
     * @param array $field
     * @return string
     */
    protected function getLabel($field)
    {
        return ($this->formBuilder->getType() == 'iblock') ? $field['NAME'] : $field['LIST_COLUMN_LABEL'];
    }

    /**
     * @param array $field
     * @return string
     */
    protected function getControlName($field)
    {
        $controlName = ($this->formBuilder->getType() == 'iblock')
            ? $field['CODE']
            : $field['FIELD_NAME'];

        if ($field['MULTIPLE'] == 'Y') {
            $controlName = sprintf('%s%s', $controlName, '[]');
        }

        return $controlName;
    }

    /**
     * @param array $field
     * @return array
     */
    protected function getValue($field)
    {
        $valueList = (isset($field['VALUE_LIST'])) ? $field['VALUE_LIST'] : array();
        foreach ($valueList as $key => $value) {
            if (!array_key_exists('VALUE', $value)) {
                $valueList[$key]['VALUE'] = $value['NAME'];
            }
        }

        return $valueList;
    }

    /**
     * @param string $fieldName
     * @return bool
     */
    protected function isDisplayField($fieldName)
    {
        return in_array($fieldName, $this->parameters->get('DISPLAY_FIELDS'));
    }

    /**
     * @return array
     */
    public function getViewData()
    {
        return $this->viewData;
    }
}