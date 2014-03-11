<?php

/*
 * This file is part of the Studio Fact package.
 *
 * (c) Kulichkin Denis (onEXHovia) <onexhovia@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * Display custom fields
 *
 * @param array $arResult
 * @param string $fieldFind
 * @return mixed
 */
function userTypePrint($arResult, $fieldFind = '')
{
    ?>
    <?
    $entityFields = $arResult['HLBLOCK']['FIELDS'];
    $valueList = $arResult['FORM'];
    $isPost = $arResult['IS_POST'];
    ?>
    <? foreach ($entityFields as $fieldName => $fieldValue): ?>

    <?
    if (!empty($fieldFind) && $fieldFind != $fieldValue['FIELD_NAME']) {
        continue;
    }
    ?>

    <?switch ($fieldValue['USER_TYPE_ID']):

        case 'string':
        case 'integer':
        case 'double':
        case 'datetime':
            ?>
            <div class="field">
                <label><?= $fieldValue['EDIT_FORM_LABEL'] ?></label>
                <input type="text" class="form-control" name="<?= $fieldValue['FIELD_NAME'] ?>"
                       value="<?= $valueList[$fieldValue['FIELD_NAME']] ?>"/>
            </div>
            <? break; ?>

        <?
        case 'enumeration': ?>
            <div class="field">
                <label><?= $fieldValue['EDIT_FORM_LABEL'] ?></label>
                <? if ($fieldValue['SETTINGS']['DISPLAY'] == 'LIST'): ?>
                    <? $multiple = ($fieldValue['MULTIPLE'] == 'Y') ? 'multiple="multiple"' : ''; ?>
                    <select name="<?= $fieldValue['FIELD_NAME'] ?>" <?= $multiple ?>>
                        <? foreach ($fieldValue['VALUE'] as $value): ?>
                            <? $selected = ($value['SELECTED'] == 'Y') ? 'selected="selected"' : ''; ?>
                            <option value="<?= $value['ID'] ?>" <?= $selected ?>><?= $value['VALUE'] ?></option>
                        <? endforeach; ?>
                    </select>
                <? else: ?>
                    <? if ($fieldValue['MULTIPLE'] == 'Y'): ?>
                        <? foreach ($fieldValue['VALUE'] as $value): ?>
                            <? $checked = ($value['SELECTED'] == 'Y') ? 'checked="checked"' : ''; ?>
                            <input type="checkbox" name="<?= $fieldValue['FIELD_NAME'] ?>"
                                   value="<?= $value['ID'] ?>" <?= $checked ?> /> <?= $value['VALUE'] ?>
                        <? endforeach; ?>
                    <? else: ?>
                        <? foreach ($fieldValue['VALUE'] as $value): ?>
                            <? $checked = ($value['SELECTED'] == 'Y') ? 'checked="checked"' : ''; ?>
                            <input type="radio" name="<?= $fieldValue['FIELD_NAME'] ?>"
                                   value="<?= $value['ID'] ?>" <?= $checked ?> /> <?= $value['VALUE'] ?>
                        <? endforeach; ?>
                    <?endif; ?>
                <?endif; ?>
            </div>
            <? break; ?>

        <?
        case 'boolean': ?>
            <?
            $defaultValue = $fieldValue['SETTINGS']['DEFAULT_VALUE'];
            $formValue = $valueList[$fieldValue['FIELD_NAME']];
            $realValue = ($isPost) ? $formValue : $defaultValue;
            ?>
            <div class="field">
                <label><?= $fieldValue['EDIT_FORM_LABEL'] ?></label>
                <? if ($fieldValue['SETTINGS']['DISPLAY'] == 'RADIO'): ?>
                    <input type="radio" name="<?= $fieldValue['FIELD_NAME'] ?>" value="1"
                           <? if ($realValue): ?>checked="checked"<? endif; ?> /> <?= GetMessage('YES') ?>
                    <input type="radio" name="<?= $fieldValue['FIELD_NAME'] ?>" value="0"
                           <? if (!$realValue): ?>checked="checked"<? endif; ?> /> <?= GetMessage('NO') ?>
                <? elseif ($fieldValue['SETTINGS']['DISPLAY'] == 'DROPDOWN'): ?>
                    <select name="<?= $fieldValue['FIELD_NAME'] ?>">
                        <option <? if ($realValue): ?>selected="selected"<? endif; ?>
                                value="1"><?= GetMessage('YES') ?></option>
                        <option <? if (!$realValue): ?>selected="selected"<? endif; ?>
                                value="0"><?= GetMessage('NO') ?></option>
                    </select>
                <?
                else: ?>
                    <input type="checkbox" name="<?= $fieldValue['FIELD_NAME'] ?>" value="1"
                           <? if ($realValue): ?>checked="checked"<? endif; ?> />
                <?endif; ?>
            </div>
            <? break; ?>

        <?
        case 'file': ?>
            <div class="field">
                <label><?= $fieldValue['EDIT_FORM_LABEL'] ?></label>
                <input type="file" name="<?= $fieldValue['FIELD_NAME'] ?>"/>
            </div>
            <? break; ?>

        <?endswitch; ?>

    <?
    if ($fieldFind == $fieldValue['FIELD_NAME']) {
        break;
    }
    ?>

<? endforeach; ?>
<? } ?>