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

use Citfact\Form\View\InputType;
use Citfact\Form\View\TextareaType;
use Citfact\Form\View\DateType;
use Citfact\Form\View\FileType;
use Citfact\Form\View\SelectType;
use Citfact\Form\View\CheckboxType;
use Citfact\Form\View\RadioType;
use Citfact\Form\View\ViewInterface;
use Citfact\Form\Type\ParameterDictionary;

class FormView
{
    private $viewType = array();
    private $viewData = array();
    private $aliasFields = array();
    private $displayFields = array();
    private $request = array();
    private $errors = array();
    private $formBuilder;
    private $parameters;
    private $formName;

    /**
     * @param FormBuilder $formBuilder
     * @param ParameterDictionary $parameters
     * @param string $formName
     */
    public function __construct(FormBuilder $formBuilder, ParameterDictionary $parameters, $formName)
    {
        $this->formBuilder = $formBuilder;
        $this->parameters = $parameters;
        $this->formName = $formName;
        $this->aliasFields = array_merge($this->aliasFields, (array)$this->parameters->get('ALIAS_FIELDS'));
        $this->displayFields = array_merge_recursive($this->displayFields, (array)$this->parameters->get('DISPLAY_FIELDS'));

        foreach ($this->getDefaultViewTypes() as $type) {
            $this->addViewType($type);
        }
    }

    /**
     * @param array $request
     */
    public function setRequest(array $request)
    {
        $this->request = $request;
    }

    /**
     * @param array $errors
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;
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
                if (!$type->detected($field, $builderType) || !$this->isDisplayField($field)) {
                    continue;
                }

                $this->viewData[$fieldName] = array(
                    'TYPE' => $type->getName(),
                    'REQUIRED' => $this->getRequired($field),
                    'MULTIPLE' => $field['MULTIPLE'],
                    'NAME' => $this->prepareControlName($field),
                    'LABEL' => $this->getLabel($field),
                    'VALUE_LIST' => $this->getValue($field),
                    'DEFAULT_VALUE' => $this->getDefaultValue($field),
                    'ERROR' => $this->getError($field),
                    'VALUE' => $this->getRequestValue($field),
                );
            }
        }

        return $this;
    }

    /**
     * @param array $field
     * @return mixed
     */
    protected function getRequestValue($field)
    {
        $controlName = $this->getControlName($field);
        if (isset($this->request[$controlName])) {
            return $this->request[$controlName];
        }

        return $this->getDefaultValue($field);
    }

    /**
     * @param array $field
     * @return string
     */
    protected function getError($field)
    {
        $controlName = $this->getControlName($field);

        return $this->errors[$controlName] ?: '';
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
        $controlName = $this->getControlName($field);
        if (isset($this->aliasFields[$controlName])) {
            return (string)$this->aliasFields[$controlName];
        }

        return ($this->formBuilder->getType() == 'iblock') ? $field['NAME'] : $field['LIST_COLUMN_LABEL'];
    }

    /**
     * @param array $field
     * @return string
     */
    protected function getDefaultValue($field)
    {
        $defaultValue = '';
        if ($this->formBuilder->getType() == 'iblock' && !is_array($field['DEFAULT_VALUE'])) {
            $defaultValue = $field['DEFAULT_VALUE'];
        } elseif (isset($field['SETTINGS']['DEFAULT_VALUE'])) {
            $defaultValue = $field['SETTINGS']['DEFAULT_VALUE'];
        }

        return $defaultValue;
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

        return $controlName;
    }

    /**
     * @param array $field
     * @return string
     */
    protected function prepareControlName($field)
    {
        $controlName = $this->getControlName($field);
        $controlName = sprintf('%s[%s]', $this->formName, $controlName);
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
     * @return ViewInterface[]
     */
    protected function getDefaultViewTypes()
    {
        return array(
            new InputType(),
            new TextareaType(),
            new DateType(),
            new FileType(),
            new SelectType(),
            new CheckboxType(),
            new RadioType(),
        );
    }

    /**
     * @param array $fieldName
     * @return bool
     */
    protected function isDisplayField($field)
    {
        $fieldName = $this->getControlName($field);
        if (empty($this->displayFields) || $this->getRequired($field) == 'Y') {
            return true;
        }

        return in_array($fieldName, $this->displayFields);
    }

    /**
     * @return array
     */
    public function getViewData()
    {
        return $this->viewData;
    }
}