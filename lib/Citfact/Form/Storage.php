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
    protected $request;

    /**
     * @var array
     */
    protected $builderData;

    /**
     * @param StorageInterface $storage
     * @param array $request
     * @param array $builderData
     */
    public function __construct(StorageInterface $storage, array $request, array $builderData)
    {
        $this->storage = $storage;
        $this->request = $request;
        $this->builderData = $builderData;
    }

    /**
     * @inheritdoc
     */
    public function save()
    {
        $this->storage->save($this->request, $this->builderData);
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