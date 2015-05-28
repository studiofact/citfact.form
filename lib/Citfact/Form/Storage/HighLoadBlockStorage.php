<?php

/*
 * This file is part of the Studio Fact package.
 *
 * (c) Kulichkin Denis (onEXHovia) <onexhovia@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Citfact\Form\Storage;

use Bitrix\Highloadblock as HL;
use Citfact\Form\Storage as BaseStorage;

class HighLoadBlockStorage extends BaseStorage
{
    /**
     * @inheritdoc
     */
    public function save(array $request, array $builderData)
    {
        // Get entity exteden DataManager
        $entity = $this->getCompileBlock($builderData['DATA']);

        // Reserve available to her field
        $request = array_intersect_key($request, $builderData['FIELDS']);

        $result = $entity::add($request);

        // If exists errors parse their
        if (!$result->isSuccess()) {
            $this->parseErrorList($result->getErrorMessages(), $builderData);
        }

        return ($result->isSuccess()) ? $result->getId() : false;
    }

    /**
     * @param array $errorList
     * @param array $builderData
     */
    protected function parseErrorList($errorList, $builderData)
    {
        $result = array();
        foreach ($builderData['FIELDS'] as $name => $field) {
            foreach ($errorList as $error) {
                if (!preg_match('#'.$field['EDIT_FORM_LABEL'].'#', $error) && $field['ERROR_MESSAGE'] != $error) {
                    continue;
                }

                if (!array_key_exists($name, $result)) {
                    $result[$name] = $error;
                }
            }
        }

        $this->errorList =  array_diff($result, array(null));
    }

    /**
     * Return compiled class exteden DataManager.
     *
     * @param array $highloadData
     *
     * @return mixed
     */
    protected function getCompileBlock($highloadData)
    {
        $enity = HL\HighloadBlockTable::compileEntity($highloadData);

        return $enity->getDataClass();
    }
}
