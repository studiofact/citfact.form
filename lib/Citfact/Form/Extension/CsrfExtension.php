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

class CsrfExtension
{
    /**
     * @var string
     */
    protected $secret;

    /**
     * Construct object.
     *
     * @param string $secret
     */
    public function __construct($secret = '')
    {
        $this->secret = $secret;
    }

    /**
     * Generates a CSRF token.
     *
     * @return string
     */
    public function generateCsrfToken()
    {
        return sha1($this->secret.$this->getSessionId());
    }

    /**
     * Validates a CSRF token.
     *
     * @param string $token
     *
     * @return bool
     */
    public function isCsrfTokenValid($token)
    {
        return $token === $this->generateCsrfToken();
    }

    /**
     * Returns the ID of the user session.
     *
     * @return string The session ID
     */
    protected function getSessionId()
    {
        if (version_compare(PHP_VERSION, '5.4', '>=')) {
            if (PHP_SESSION_NONE === session_status()) {
                session_start();
            }
        } elseif (!session_id()) {
            session_start();
        }

        return session_id();
    }
}
