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

class Mailer implements MailerInterface
{
    /**
     * @var \Citfact\Form\Type\ParameterDictionary
     */
    protected $parameters;

    /**
     * @var \CEventType
     */
    protected $eventType;

    /**
     * @var \CEvent
     */
    protected $event;

    /**
     * @param \CEventType         $eventType
     * @param \CEvent             $event
     * @param ParameterDictionary $parameters
     */
    public function __construct(ParameterDictionary $parameters, \CEventType $eventType, \CEvent $event)
    {
        $this->parameters = $parameters;
        $this->eventType = $eventType;
        $this->event = $event;
    }

    /**
     * {@inheritdoc}
     */
    public function send(array $data, array $attachFiles = array())
    {
        $eventName = $this->parameters->get('EVENT_NAME');
        $eventTemplate = (is_numeric($this->parameters->get('EVENT_TEMPLATE')))
            ? $this->parameters->get('EVENT_TEMPLATE')
            : '';

        $eventType = $this->eventType->getList(array('EVENT_NAME' => $eventName))->getNext();
        if ($eventName && !is_array($eventType)) {
            return false;
        }

        if ($this->parameters->get('EVENT_TYPE') == 'IMMEDIATE') {
            return $this->sendImmediate($eventName, $eventTemplate, $data, $attachFiles);
        }

        return $this->sendDefault($eventName, $eventTemplate, $data, $attachFiles);
    }

    /**
     * Creates a post event which will subsequently be sent as email message.
     *
     * @param string $event
     * @param mixed  $template
     * @param array  $data
     * @param array  $attachFiles
     *
     * @return mixed
     */
    protected function sendDefault($event, $template, $data, $attachFiles)
    {
        return $this->event->send($event, SITE_ID, $data, 'Y', $template, $attachFiles);
    }

    /**
     * Sends the message immediately.
     *
     * @param string $event
     * @param mixed  $template
     * @param array  $data
     * @param array  $attachFiles
     *
     * @return mixed
     */
    protected function sendImmediate($event, $template, $data, $attachFiles)
    {
        return $this->event->sendImmediate($event, SITE_ID, $data, 'Y', $template, $attachFiles);
    }
}
