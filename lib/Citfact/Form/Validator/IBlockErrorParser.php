<?php

/*
 * This file is part of the Studio Fact package.
 *
 * (c) Kulichkin Denis (onEXHovia) <onexhovia@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Citfact\Form\Validator;

class IBlockErrorParser
{
    /**
     * @var array
     */
    private $fields;

    /**
     * @var array
     */
    private $defaultFields;

    /**
     * @var array
     */
    protected $fieldToCode = array(
        'NAME'            => 'IBLOCK_BAD_ELEMENT_NAME',
        'ACTIVE_FROM'     => 'IBLOCK_BAD_ACTIVE_FROM',
        'ACTIVE_TO'       => 'IBLOCK_BAD_ACTIVE_TO',
        'PREVIEW_PICTURE' => 'IBLOCK_ERR_PREVIEW_PICTURE',
        'DETAIL_PICTURE'  => 'IBLOCK_ERR_DETAIL_PICTURE',
        'XML_ID'          => 'IBLOCK_BAD_EXTERNAL_CODE',
        'CODE'            => 'IBLOCK_DUP_ELEMENT_CODE',
        'CODE'            => 'IBLOCK_DUP_ELEMENT_CODE',
    );

    /**
     * @param array $fields
     * @param array $defaultFields
     */
    public function __construct($fields, $defaultFields)
    {
        $this->fields = $fields;
        $this->defaultFields = $defaultFields;
    }

    /**
     * @param string
     *
     * @return array
     */
    public function parse($errors)
    {
        $errorsList = $this->toArray($errors);
        $errorsResult = array();

        foreach ($errorsList as $keyError => $error) {
            foreach ($this->fieldToCode as $field => $codeError) {
                $tmpTextError = GetMessage('IBLOCK_BAD_FIELD', array('#FIELD_NAME#' => $field));
                if (GetMessage($codeError) == $error || $tmpTextError == $error) {
                    $errorsResult[$field] = $error;
                    unset($errorsList[$keyError]);
                    break;
                }
            }
        }

        foreach ($this->defaultFields as $field) {
            foreach ($errorsList as $keyError => $error) {
                $tmpTextError = GetMessage('IBLOCK_BAD_FIELD', array('#FIELD_NAME#' => $field['NAME']));
                if ($tmpTextError == $error) {
                    $errorsResult[$field['CODE']] = $error;
                    unset($errorsList[$keyError]);
                }
            }
        }

        foreach ($this->fields as $field) {
            foreach ($errorsList as $keyError => $error) {
                $tmpTextError = GetMessage('IBLOCK_BAD_PROPERTY', array('#PROPERTY#' => $field['NAME']));
                if ($tmpTextError == $error) {
                    $errorsResult[$field['CODE']] = $error;
                    unset($errorsList[$keyError]);
                }
            }
        }

        if (sizeof($errorsList) > 0) {
            foreach ($errorsList as $error) {
                if (GetMessage('IBLOCK_ERR_FILE_PROPERTY') == $error) {
                    $errorsResult['ERROR_FILE_PROPERTY'] = $error;
                } elseif (mb_strpos(GetMessage('FILE_BAD_TYPE'), $error) !== false) {
                    $errorsResult['ERROR_FILE_BAD_TYPE'] = $error;
                }
            }
        }

        return $errorsResult;
    }

    /**
     * @param string $errors
     * @param string $delimiter
     *
     * @return array
     */
    protected function toArray($errors, $delimiter = '<br>')
    {
        $array = array_diff(explode($delimiter, $errors), array(null));
        $array = array_map('trim', $array);

        return $array;
    }
}
