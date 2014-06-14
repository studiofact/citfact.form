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

use Citfact\Form\Type\ParameterDictionary;

class Mailer
{
    /**
     * @var \Citfact\Form\Type\ParameterDictionary
     */
    protected $parameters;

    /**
     * @param ParameterDictionary $parameters
     */
    public function __construct(ParameterDictionary $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Send message
     *
     * @param array $data
     * @return mixed
     */
    public function send($data)
    {
        $eventName = $this->parameters->get('EVENT_NAME');
        $eventTemplate = (is_numeric($this->parameters->get('EVENT_TEMPLATE')))
            ? $this->parameters->get('EVENT_TEMPLATE')
            : '';

        $eventType = $this->getEventType()->getList(array('EVENT_NAME' => $eventName))->getNext();
        if ($eventName && is_array($eventType)) {
            return false;
        }

        if ($this->parameters->get('EVENT_TYPE') == 'IMMEDIATE') {
            return $this->sendImmediate($eventName, $eventTemplate, $data);
        }

        return $this->sendDefault($eventName, $eventTemplate, $data);
    }

    /**
     * Creates a post event which will subsequently be sent as email message
     *
     * @param string $event
     * @param mixed $template
     * @param array $data
     * @return mixed
     */
    protected function sendDefault($event, $template, $data)
    {
        return $this->getEvent()->send($event, SITE_ID, $data, 'Y', $template);
    }

    /**
     * Sends the message immediately
     *
     * @param string $event
     * @param mixed $template
     * @param array $data
     * @return mixed
     */
    protected function sendImmediate($event, $template, $data)
    {
        return $this->getEvent()->sendImmediate($event, SITE_ID, $data, 'Y', $template);
    }

    /**
     * @return \CEventType
     */
    protected function getEventType()
    {
        return new \CEventType();
    }

    /**
     * @return \CEvent
     */
    protected function getEvent()
    {
        return new \CEvent();
    }
}