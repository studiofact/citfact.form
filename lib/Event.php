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

use Bitrix\Main\Event as BaseEvent;

class Event
{
    /**
     * Store ID module for which will caused by events
     */
    const MODULE_ID = 'citfact.form';

    /**
     * Trigger event
     *
     * @param string $name
     * @param array $params
     */
    protected function trigger($name, $params)
    {
        $event = new BaseEvent(self::MODULE_ID, $name);
        $event->setParameters($params);
        $event->send();
    }

    /**
     * Magic finders.
     *
     * @param string $method
     * @param array $arguments
     * @throws \BadMethodCallException If the method called is an invalid find* method.
     */
    public function __call($method, $arguments)
    {
        switch ($method) {
            case 'onAfterBuilder':
            case 'onBeforeStorage':
            case 'onAfterStorage':
                $this->trigger($method, $arguments);
                break;

            default:
                throw new \BadMethodCallException('Undefined method '.$method);
        }
    }
}