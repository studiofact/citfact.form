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

class Storage
{
    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @param StorageInterface $storage
     */
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param array $request
     * @param array $builderData
     */
    public function save(array $request, array $builderData)
    {
        $this->storage->save($request, $builderData);
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return (sizeof($this->getErrors()) > 0) ? false : true;
    }

    /**
     * @inheritdoc
     */
    public function getErrors()
    {
        return $this->storage->getErrors();
    }
}