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

class CaptchaExtension
{
    /**
     * @var \CMain
     */
    private $CMain;

    /**
     * Construct object
     */
    public function __construct()
    {
        $this->CMain = new \CMain();
    }

    /**
     * Generates a Captcha token
     *
     * @return string
     */
    public function generateCaptchaToken()
    {
        return $this->getCaptchaToken();
    }

    /**
     * Validates a Captcha token
     *
     * @param string $response
     * @param string $token
     * @return bool
     */
    public function isCaptchaTokenValid($response, $token)
    {
        return $this->CMain->captchaCheckCode($response, $token);
    }

    /**
     * Return a captcha token
     *
     * @return string
     */
    protected function getCaptchaToken()
    {
        return $this->CMain->captchaGetCode();
    }

}