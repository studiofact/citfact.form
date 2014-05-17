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

class HighLoadManager
{
    /**
     * @var HighLoadGenerator
     */
    protected $generator;

    /**
     * Construct object
     *
     * @param HighLoadGenerator $highLoadGenerator
     */
    public function __construct(HighLoadGenerator $highLoadGenerator)
    {
        $this->generator = $highLoadGenerator;
    }

    /**
     * Checks the list of custom field values ​​for activity
     *
     * @param array $postData
     * @return void
     */
    public function checkValueSelected($postData)
    {
        $highLoadBLockFields = $this->generator->getHighLoadBlockFields();
        foreach ($highLoadBLockFields as $fieldName => $field) {
            $availableType = array('enumeration', 'iblock_section', 'iblock_element');
            if (!in_array($field['USER_TYPE_ID'], $availableType)) {
                continue;
            }

            $formValue = (array_key_exists($fieldName, $postData)) ? $postData[$fieldName] : '';
            foreach ($field['VALUE'] as $key => $value) {
                $highLoadBLockFields[$fieldName]['VALUE'][$key]['SELECTED'] =
                    (is_array($formValue))
                        ? (in_array($value['ID'], $formValue)) ? 'Y' : 'N'
                        : ($value['ID'] == $formValue) ? 'Y' : 'N';
            }
        }

        $this->generator->setHighLoadBlockFields($highLoadBLockFields);
    }


    /**
     * Parsing errors after working method CheckFields
     *
     * @param array $errorList
     * @return array
     */
    public function parseErrorList($errorList)
    {
        $result = array();
        if (array_key_exists('captcha_word', $errorList)) {
            $result['captcha_word'] = $errorList['captcha_word'];
            unset($errorList['captcha_word']);
        }

        $errorList = (array_key_exists('internal', $errorList))
            ? $errorList['internal']
            : explode('<br>', $errorList[0]);

        if (is_object($errorList)) {
            foreach ($errorList->messages as $error) {
                if (!array_key_exists($error['id'], $result)) {
                    $result[$error['id']] = $error['text'];
                }
            }
        } else {
            foreach ($this->generator->getHighLoadBlockFields() as $name => $field) {
                foreach ($errorList as $error) {
                    if (!preg_match('#' . $field['EDIT_FORM_LABEL'] . '#', $error) && $field['ERROR_MESSAGE'] != $error) {
                        continue;
                    }

                    if (!array_key_exists($name, $result)) {
                        $result[$name] = $error;
                    }
                }
            }
        }

        return array_diff($result, array(null));
    }

    /**
     * Adding a post event
     *
     * @param string $eventName
     * @param int $eventTemplate
     * @param array $data
     * @return void
     */
    public function addEmailEvent($eventName, $eventTemplate, $data)
    {
        $eventTemplate = (is_numeric($eventTemplate)) ? $eventTemplate : '';

        $eventType = \CEventType::GetList(array('EVENT_NAME' => $eventName))->GetNext();
        if ($eventName && is_array($eventType)) {
            \CEvent::send($eventName, SITE_ID, $data, 'Y', $eventTemplate);
        }
    }

    /**
     * Generate array of fields to display in the form
     *
     * @param array $availableFields
     * @param array $textareaFields
     * @return array
     */
    public function getDisplayFields($availableFields, $textareaFields)
    {
        $displayList = array();
        foreach ($this->generator->getHighLoadBlockFields() as $fieldName => $field) {
            if (!in_array($fieldName, $availableFields)) {
                continue;
            }

            $displayList[$fieldName] = $field;
            $displayList[$fieldName]['~USER_TYPE_ID'] = $field['USER_TYPE_ID'];
            $displayList[$fieldName]['USER_TYPE_ID'] =
                (in_array($fieldName, $textareaFields)) ? 'textarea' : $field['USER_TYPE_ID'];
        }

        return $displayList;
    }
}
