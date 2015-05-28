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

class CsrfExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testCsrf()
    {
        $csrf = new CsrfExtension();
        $csrfToken = $csrf->generateCsrfToken();
        $this->assertNotEmpty($csrfToken);
        $this->assertTrue($csrf->isCsrfTokenValid($csrfToken));

        $csrf = new CsrfExtension('secrectKey');
        $csrfToken = $csrf->generateCsrfToken();
        $this->assertNotEmpty($csrfToken);
        $this->assertTrue($csrf->isCsrfTokenValid($csrfToken));
    }
}
