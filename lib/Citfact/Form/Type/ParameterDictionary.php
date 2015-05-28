<?php

/*
 * This file is part of the Studio Fact package.
 *
 * (c) Kulichkin Denis (onEXHovia) <onexhovia@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Citfact\Form\Type;

use Bitrix\Main\Type\ParameterDictionary as BaseParameterDictionary;

class ParameterDictionary extends BaseParameterDictionary
{
    /**
     * Set cell dictionary.
     *
     * @param mixed $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        $this->values[$key] = $value;
    }
}
