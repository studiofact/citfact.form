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
        $data = $this->convertMacrosValueList($data);
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
     * @return array
     */
    private function convertMacrosValueList(array $macrosData)
    {
        foreach ($macrosData as $key => $value) {
            if (!isset($this->viewData[$key])) {
                continue;
            }

            $viewData = $this->viewData[$key];
            if (is_array($value) && $viewData[$key]['MULTIPLE'] == 'N') {
                continue;
            }

            $macrosData[$key] = $this->resolveValue($viewData, $value);
        }

        return $macrosData;
    }

    /**
     * @param array      $viewData
     * @param string|int $value
     *
     * @return string
     */
    private function resolveValue($viewData, $value)
    {
        if (empty($viewData['VALUE_LIST'])) {
            return $value;
        }

        foreach ($viewData['VALUE_LIST'] as $valueItem) {
            if ($valueItem['ID'] == $value) {
                $value = $valueItem['NAME'];
            }

            if (is_array($value)) {
                $key = array_search($valueItem['ID'], $value);
                if (false !== $key) {
                    $value[$key] = $valueItem['NAME'];
                }
            }
        }

        return $value;
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
            if (!isset($this->viewData[$key])) {
                continue;
            }

            $viewData = $this->viewData[$key];
            if (is_array($value) && $viewData['MULTIPLE'] == 'N') {
                continue;
            }

            $label = $viewData['LABEL'];
            $printValue = is_array($value) ? implode('/', $value) : $value;

            $macrosJoin .= sprintf('<strong>%s</strong> - %s<br/>', $label, $printValue);
        }

        return $macrosJoin;
    }
}