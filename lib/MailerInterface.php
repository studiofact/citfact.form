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

interface MailerInterface
{
    /**
     * Send message
     *
     * @param array $data
     * @return mixed
     */
    public function send(array $data);
}