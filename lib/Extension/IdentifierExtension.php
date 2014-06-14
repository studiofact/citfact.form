<?php

/*
 * This file is part of the Studio Fact package.
 *
 * (c) Kulichkin Denis (onEXHovia) <onexhovia@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Citfact\Form\Extension;

class IdentifierExtension
{
    /**
     * Generates a identifier
     *
     * @return string
     */
    public function generateIdentifier()
    {
        return $this->getIdentifier();
    }

    /**
     * Validates a identifier token
     *
     * @param string $identifier
     * @return bool
     */
    public function  isIdentifierValid($identifier)
    {
        return $identifier === $this->generateIdentifier();
    }

    /**
     * Return identifier token
     *
     * @return string
     */
    protected function getIdentifier()
    {
        $caller = debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS);
        $caller = $caller[sizeof($caller) - 1];

        return (string)crc32(sprintf('%s_%s' ,$caller['file'], $caller['line']));
    }
}