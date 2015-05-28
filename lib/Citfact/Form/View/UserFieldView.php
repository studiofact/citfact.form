<?php

/*
 * This file is part of the Studio Fact package.
 *
 * (c) Kulichkin Denis (onEXHovia) <onexhovia@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Citfact\Form\View;

use Citfact\Form\FormView;
use Citfact\Form\View\Type\UserField\CheckboxType;
use Citfact\Form\View\Type\UserField\DateType;
use Citfact\Form\View\Type\UserField\FileType;
use Citfact\Form\View\Type\UserField\RadioType;
use Citfact\Form\View\Type\UserField\InputType;
use Citfact\Form\View\Type\UserField\SelectType;

class UserFieldView extends FormView
{
    /**
     * @inheritdoc
     */
    public function getDefaultViewTypes()
    {
        return array(
            new InputType(),
            new DateType(),
            new FileType(),
            new SelectType(),
            new CheckboxType(),
            new RadioType(),
        );
    }
}
