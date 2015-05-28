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

abstract class Storage implements StorageInterface
{
    /**
     * @var array
     */
    protected $errorList = array();

    /**
     * @inheritdoc
     */
    public function getErrors()
    {
        return $this->errorList;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return (sizeof($this->errorList) > 0) ? false : true;
    }
}
