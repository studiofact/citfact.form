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

use Citfact\Form\View\Type\TypeInterface;

abstract class FormView implements FormViewInterface
{
    protected $viewType = array();
    protected $viewData = array();
    protected $aliasFields = array();
    protected $displayFields = array();
    protected $request = array();
    protected $errors = array();
    protected $formBuilder;
    protected $formName;

    /**
     * @param FormBuilderInterface $formBuilder
     */
    public function __construct(FormBuilderInterface $formBuilder)
    {
        $this->formBuilder = $formBuilder;
    }

    /**
     * @param string $formName
     */
    public function setFormName($formName)
    {
        $this->formName = $formName;

        return $this;
    }

    /**
     * @param array $aliasFields
     */
    public function setAliasFields(array $aliasFields)
    {
        $this->aliasFields = $aliasFields;

        return $this;
    }

    /**
     * @param array $displayFields
     */
    public function setDisplayFields(array $displayFields)
    {
        $this->displayFields = $displayFields;

        return $this;
    }

    /**
     * @param array $request
     */
    public function setRequest(array $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @param array $errors
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     * @param TypeInterface $type
     * @return $this
     */
    public function addViewType(TypeInterface $type)
    {
        if (!isset($this->viewType[$type->getName()])) {
            $this->viewType[$type->getName()] = $type;
        }

        return $this;
    }

    /**
     * return array
     */
    protected function getBuilderData()
    {
        return $this->formBuilder->getBuilderData();
    }

    /**
     * @return $this
     */
    public function create()
    {
        foreach ($this->getDefaultViewTypes() as $type) {
            $this->addViewType($type);
        }

        $builderData = $this->getBuilderData();
        foreach ($builderData['FIELDS'] as $field) {
            foreach ($this->viewType as $type) {
                $type->setFieldData($field);
                $controlName = $type->getControlName();
                $required = $type->getRequired();
                if (!$type->detected() || !$this->isDisplayField($controlName, $required)) {
                    continue;
                }

                $defaultValue = $type->getDefaultValue();
                $this->viewData[$controlName] = array(
                    'TYPE' => $type->getName(),
                    'REQUIRED' => $required,
                    'MULTIPLE' => $type->getMultiple(),
                    'NAME' => $this->prepareControlName($controlName),
                    'LABEL' => $type->getLabel(),
                    'VALUE_LIST' => $type->getValueList(),
                    'DEFAULT_VALUE' => $defaultValue,
                    'ERROR' => $this->getError($controlName),
                    'VALUE' => $this->getRequestValue($controlName, $defaultValue),
                );
            }
        }

        return $this;
    }

    /**
     * @param string $controlName
     * @param string $defaultValue
     * @return mixed
     */
    protected function getRequestValue($controlName, $defaultValue)
    {
        if (isset($this->request[$controlName])) {
            return htmlspecialchars($this->request[$controlName]);
        }

        return htmlspecialchars($defaultValue);
    }

    /**
     * @param string $controlName
     * @return string
     */
    protected function getError($controlName)
    {
        return $this->errors[$controlName] ?: '';
    }

    /**
     * @param string $controlName
     * @param string $multiple
     * @return string
     */
    protected function prepareControlName($controlName, $multiple)
    {
        if (!$this->formName) {
            throw new \RuntimeException('Form name empty, use setFormName()');
        }

        $controlName = sprintf('%s[%s]', $this->formName, $controlName);
        if ($multiple == 'Y') {
            $controlName = sprintf('%s%s', $controlName, '[]');
        }

        return $controlName;
    }

    /**
     * @param string $controlName
     * @param string $required
     * @return bool
     */
    protected function isDisplayField($controlName, $required)
    {
        if (empty($this->displayFields) || $required == 'Y') {
            return true;
        }

        return in_array($controlName, $this->displayFields);
    }

    /**
     * @return array
     */
    public function getViewData()
    {
        return $this->viewData;
    }
}
