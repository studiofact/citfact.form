<?php

/*
 * This file is part of the Studio Fact package.
 *
 * (c) Kulichkin Denis (onEXHovia) <onexhovia@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Citfact\Form\Attach;

interface AttachInterface
{
    /**
     * @param int   $insertId
     * @param array $attachFields
     *
     * @return array
     */
    public function getFiles($insertId, array $attachFields);
}
