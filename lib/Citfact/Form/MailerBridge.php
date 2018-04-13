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

class MailerBridge implements MailerInterface
{
    /**
     * @var MailerInterface
     */
    private $mailerInner;

    /**
     * @var FormBuilderInterface
     */
    private $builder;

    /**
     * @var array
     */
    private $viewData = array();

    /**
     * @param MailerInterface      $mailerInner
     * @param FormBuilderInterface $builder
     */
    public function __construct(MailerInterface $mailerInner, FormBuilderInterface $builder)
    {
        $this->mailerInner = $mailerInner;
        $this->builder = $builder;
    }

    /**
     * {@inheritdoc}
     */
    public function send(array $data, array $attachFiles = array())
    {
        $data['MACROS_JOIN'] = $this->macrosJoin($data);

        $this->mailerInner->send($data, $attachFiles);
    }

    /**
     * @param array $viewData
     */
    public function setViewData(array $viewData)
    {
        $this->viewData = $viewData;
    }

    /**
     * @param array $macrosData
     *
     * @return string
     */
    private function macrosJoin(array $macrosData)
    {
        $parameters = array(
            'VIEW_DATA' => $this->viewData,
            'MACROS' => $macrosData,
        );

        $event = new Event(FormEvents::MACROS_JOIN, $parameters, $this->builder);
        $event->send();

        if ($overrideMacrosJoin = $event->getOverrideMacrosJoin()) {
            return $overrideMacrosJoin;
        }

        $macrosJoin = '';
        foreach ($macrosData as $key => $value) {
            if (!isset($this->viewData[$key]) || is_array($value)) {
                continue;
            }

            $label = $this->viewData[$key]['LABEL'];
            $macrosJoin .= sprintf('<strong>%s</strong> - %s<br/>', $label, $value);
        }

        return $macrosJoin;
    }
}