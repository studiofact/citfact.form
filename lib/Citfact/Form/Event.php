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
     * @param array $parameters
     */
    public function __construct($eventName, array $parameters = array())
    {
				echo $eventName;
        if (FormEvents::BUILD != $eventName &&
            FormEvents::PRE_STORAGE != $eventName &&
            FormEvents::STORAGE != $eventName
        ) {
            throw new \InvalidArgumentException('Invalid event name, see FormEvents');
        }

        parent::__construct(self::MODULE_ID, $eventName, $parameters);
    }

    /**
     * Merges the data fields set in the event handlers with the source fields.
     * Returns a merged array of the data fields from the all event handlers.
     *
     * @param array $data
     * @return array
     */
    public function mergeFields(array $data)
    {
        if ($this->getResults() == null) {
            return $data;
        }

        foreach ($this->getResults() as $evenResult) {
            if ($evenResult->getResultType() !== EventResult::ERROR) {
                $removed = $evenResult->getUnset();
                foreach ($removed as $val) {
                    unset($data[$val]);
                }

                $modified = $evenResult->getModified();
                if (!empty($modified)) {
                    $data = array_merge($data, $modified);
                }
            }
        }

        return $data;
    }
}