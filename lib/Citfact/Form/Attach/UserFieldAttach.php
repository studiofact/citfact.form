<?php

/*
 * This file is part of the Studio Fact package.
 *
 * (c) Kulichkin Denis (onEXHovia) <onexhovia@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Citfact\Form\Attach;

use Bitrix\Highloadblock as HL;

class UserFieldAttach extends AbstractAttach
{
    /**
     * {@inheritdoc}
     */
    public function getFiles($insertId, array $attachFields)
    {
        $filesList = array();
        $builderData = $this->builder->getBuilderData();

        $enity = HL\HighloadBlockTable::compileEntity($builderData['DATA']);
        $enity = $enity->getDataClass();

        $resultDb = $enity::getList(array('filter' => array('ID' => $insertId)));
        if (!($fields = $resultDb->fetch())) {
            return $filesList;
        }

        foreach ($fields as $key => $value) {
            if (!in_array($key, $attachFields)) {
                continue;
            }

            if (is_numeric($value)) {
                $filesList[] = $value;
            } elseif (is_array($value)) {
                $filesList = array_merge_recursive($filesList, $value);
            }
        }

        return $filesList;
    }
}
