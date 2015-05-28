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

class CaptchaExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testCaptcha()
    {
        $captcha = new CaptchaExtension();
        $captchaToken = $captcha->generateCaptchaToken();
        $captchaCode = $captcha->getCaptchaCode();
        $this->assertTrue($captcha->isCaptchaTokenValid($captchaCode, $captchaToken));
    }
}
