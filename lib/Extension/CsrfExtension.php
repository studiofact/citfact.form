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
     * Construct object
     *
     * @param string $secret
     */
    public function __construct($secret = '')
    {
        $this->secret = $secret;
    }

    /**
     * Generates a CSRF token
     *
     * @param string $intention
     * @return string
     */
    public function generateCsrfToken($intention)
    {
        return sha1($this->secret.$intention.$this->getSessionId());
    }

    /**
     * Validates a CSRF token.
     *
     * @param string $intention
     * @param string $token
     * @return bool
     */
    public function isCsrfTokenValid($intention, $token)
    {
        return $token === $this->generateCsrfToken($intention);
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