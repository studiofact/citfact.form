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
use Citfact\Form\View\Type\IBlock\CheckboxType;
use Citfact\Form\View\Type\IBlock\DateType;
use Citfact\Form\View\Type\IBlock\FileType;
use Citfact\Form\View\Type\IBlock\RadioType;
use Citfact\Form\View\Type\IBlock\InputType;
use Citfact\Form\View\Type\IBlock\SelectType;
use Citfact\Form\View\Type\IBlock\TextareaType;

class IBlockView extends FormView
{
    /**
     * @return array
     */
    protected function getBuilderData()
    {
        $builderData = $this->formBuilder->getBuilderData();
        $builderData['FIELDS'] = $builderData['DEFAULT_FIELDS'] + $builderData['FIELDS'];

        return $builderData;
    }

    /**
     * @inheritdoc
     */
    public function getDefaultViewTypes()
    {
        return array(
            new InputType(),
            new TextareaType(),
            new DateType(),
            new FileType(),
            new SelectType(),
            new CheckboxType(),
            new RadioType(),
        );
    }
}
