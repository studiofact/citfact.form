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

class FormView
{
    private $viewType = array();
    private $viewData = array();
    private $formBuilder;

    /**
     * @param FormBuilder $formBuilder
     */
    public function __construct(FormBuilder $formBuilder)
    {
        $this->formBuilder = $formBuilder;
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
        foreach ($builderData['FIELDS'] as $fieldName => $field) {
            foreach ($this->viewType as $type) {
                if (!$type->detected($field, $builderType)) {
                    continue;
                }

                $label = ($builderType == 'iblock') ? $field['NAME'] : $field['LIST_COLUMN_LABEL'];
                $required = ($builderType == 'iblock') ? $field['IS_REQUIRED'] : $field['MANDATORY'];
                $valueList = (isset($field['VALUE_LIST'])) ? $field['VALUE_LIST'] : array();
                foreach ($valueList as $key => $value) {
                    if (!array_key_exists('VALUE', $value)) {
                        $valueList[$key]['VALUE'] = $value['NAME'];
                    }
                }

                $controlName = $fieldName;
                if ($builderType == 'iblock') {
                    $controlName = sprintf('%s%s', $controlName, ($field['MULTIPLE'] == 'Y') ? '[]' : '');
                }

                $this->viewData[$fieldName] = array(
                    'TYPE' => $type->getName(),
                    'REQUIRED' => $required,
                    'MULTIPLE' => $field['MULTIPLE'],
                    'NAME' => $controlName,
                    'LABEL' => $label,
                    'VALUE_LIST' => $valueList,
                );
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getViewData()
    {
        return $this->viewData;
    }
}