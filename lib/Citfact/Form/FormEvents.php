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

final class FormEvents
{
    /**
     * Activated after a successful data collection forms.
     */
    const BUILD = 'onAfterBuilder';

    /**
     * Activated before to add an entry to storage.
     */
    const PRE_STORAGE = 'onBeforeStorage';

    /**
     * Activated after a successful record in storage before calling the mail event.
     */
    const STORAGE = 'onAfterStorage';

    /**
     * Activated before macro merges
     */
    const MACROS_JOIN = 'onMacrosJoin';
}
