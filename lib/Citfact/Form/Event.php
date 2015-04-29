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

class Event extends BaseEvent
{
    /**
     * Store ID module for which will caused by events
     */
    const MODULE_ID = 'citfact.form';

    /**
     * @param string $eventName
     * @param array  $parameters
     */
    public function __construct($eventName, array $parameters = array())
    {
        parent::__construct(self::MODULE_ID, $eventName, $parameters);
    }
}