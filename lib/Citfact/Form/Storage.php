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
     * @var array
     */
    protected $builderData;

    /**
     * @param StorageInterface $storage
     * @param array $builderData
     */
    public function __construct(StorageInterface $storage, array $builderData)
    {
        $this->storage = $storage;
        $this->builderData = $builderData;
    }

    /**
     * @param array $request
     */
    public function save(array $request)
    {
        $this->storage->save($request, $this->builderData);
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