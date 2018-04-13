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

/**
 * {@internal}
 */
class Event extends BaseEvent
{
    /**
     * Store ID module for which will caused by events.
     */
    const MODULE_ID = 'citfact.form';

    /**
     * @var FormBuilderInterface
     */
    private $builder;

    /**
     * @param string               $eventName
     * @param array                $parameters
     * @param FormBuilderInterface $builder
     */
    public function __construct($eventName, array $parameters = array(), FormBuilderInterface $builder)
    {
        if (FormEvents::BUILD != $eventName &&
            FormEvents::PRE_STORAGE != $eventName &&
            FormEvents::MACROS_JOIN != $eventName &&
            FormEvents::STORAGE != $eventName
        ) {
            throw new \InvalidArgumentException(sprintf('Invalid event name, see %s', FormEvents::class));
        }

        $this->builder = $builder;

        parent::__construct(self::MODULE_ID, $eventName, $parameters);
    }

    /**
     * @return FormBuilderInterface
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * @return null|string
     */
    public function getOverrideMacrosJoin()
    {
        $macrosJoin = null;
        if ($this->getResults() == null) {
            return $macrosJoin;
        }

        /** @var EventResult $evenResult */
        foreach ($this->getResults() as $evenResult) {
            if (!$evenResult instanceof EventResult) {
                continue;
            }

            $macrosJoin = $evenResult->getMacrosJoin();
        }

        return $macrosJoin;
    }

    /**
     * Merges the data fields set in the event handlers with the source fields.
     * Returns a merged array of the data fields from the all event handlers.
     *
     * @param array $data
     *
     * @return array
     */
    public function mergeFields(array $data)
    {
        if ($this->getResults() == null) {
            return $data;
        }

        /** @var EventResult $evenResult */
        foreach ($this->getResults() as $evenResult) {
            if (!$evenResult instanceof EventResult) {
                continue;
            }

            $removed = $evenResult->getUnset();
            foreach ($removed as $val) {
                unset($data[$val]);
            }

            $modified = $evenResult->getModified();
            if (!empty($modified)) {
                $data = array_merge($data, $modified);
            }
        }

        return $data;
    }
}
