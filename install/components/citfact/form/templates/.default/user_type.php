<?php

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
$userTypePrint = function ($arResult, $fieldFind = '') {
    ?>
    <?
    $entityFields = $arResult['HLBLOCK']['DISPLAY_FIELDS'];
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
                <div
                    class="form-group"
                    data-required="<?= ($fieldValue['MANDATORY'] == 'Y') ? 'true' : 'false' ?>"
                    data-regexp="<?= $fieldValue['SETTINGS']['REGEXP'] ?>"
                    data-min-length="<?= $fieldValue['SETTINGS']['MIN_LENGTH'] ?>"
                    data-max-length="<?= $fieldValue['SETTINGS']['MAX_LENGTH'] ?>"
                    >
                    <label><?= $fieldValue['EDIT_FORM_LABEL'] ?></label>
                    <input type="text" class="form-control" name="<?= $fieldValue['FIELD_NAME'] ?>"
                           value="<?= $valueList[$fieldValue['FIELD_NAME']] ?>"/>
                </div>
                <? break; ?>

            <?
            case 'textarea':
                ?>
                <div class="form-group"
                     data-required="<?= ($fieldValue['MANDATORY'] == 'Y') ? 'true' : 'false' ?>"
                     data-regexp="<?= $fieldValue['SETTINGS']['REGEXP'] ?>"
                     data-min-length="<?= $fieldValue['SETTINGS']['MIN_LENGTH'] ?>"
                     data-max-length="<?= $fieldValue['SETTINGS']['MAX_LENGTH'] ?>"
                    >
                    <label><?= $fieldValue['EDIT_FORM_LABEL'] ?></label>
                    <textarea class="form-control"
                              name="<?= $fieldValue['FIELD_NAME'] ?>"><?= $valueList[$fieldValue['FIELD_NAME']] ?></textarea>
                </div>
                <? break; ?>

            <?
            case 'enumeration':
            case 'iblock_section':
            case 'iblock_element':
                ?>
                <? $keyValue = ($fieldValue['USER_TYPE_ID'] == 'enumeration') ? 'VALUE' : 'NAME'; ?>
                <div class="form-group" data-required="<?= ($fieldValue['MANDATORY'] == 'Y') ? 'true' : 'false' ?>">
                    <label><?= $fieldValue['EDIT_FORM_LABEL'] ?></label>
                    <? if ($fieldValue['SETTINGS']['DISPLAY'] == 'LIST'): ?>
                        <? $multiple = ($fieldValue['MULTIPLE'] == 'Y') ? 'multiple="multiple"' : ''; ?>
                        <select class="form-control" name="<?= $fieldValue['FIELD_NAME'] ?>" <?= $multiple ?>>
                            <? foreach ($fieldValue['VALUE'] as $value): ?>
                                <? $selected = ($value['SELECTED'] == 'Y') ? 'selected="selected"' : ''; ?>
                                <option value="<?= $value['ID'] ?>" <?= $selected ?>><?= $value[$keyValue] ?></option>
                            <? endforeach; ?>
                        </select>
                    <? else: ?>
                        <? if ($fieldValue['MULTIPLE'] == 'Y'): ?>
                            <? foreach ($fieldValue['VALUE'] as $value): ?>
                                <? $checked = ($value['SELECTED'] == 'Y') ? 'checked="checked"' : ''; ?>
                                <input type="checkbox" name="<?= $fieldValue['FIELD_NAME'] ?>"
                                       value="<?= $value['ID'] ?>" <?= $checked ?> /> <?= $value[$keyValue] ?>
                            <? endforeach; ?>
                        <? else: ?>
                            <? foreach ($fieldValue['VALUE'] as $value): ?>
                                <? $checked = ($value['SELECTED'] == 'Y') ? 'checked="checked"' : ''; ?>
                                <input type="radio" name="<?= $fieldValue['FIELD_NAME'] ?>"
                                       value="<?= $value['ID'] ?>" <?= $checked ?> /> <?= $value[$keyValue] ?>
                            <? endforeach; ?>
                        <?endif; ?>
                    <?endif; ?>
                </div>
                <? break; ?>

            <?
            case 'boolean':
                ?>
                <?
                $defaultValue = $fieldValue['SETTINGS']['DEFAULT_VALUE'];
                $formValue = $valueList[$fieldValue['FIELD_NAME']];
                $realValue = ($isPost) ? $formValue : $defaultValue;
                ?>
                <div class="form-group" data-required="<?= ($fieldValue['MANDATORY'] == 'Y') ? 'true' : 'false' ?>">
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
            case 'file':
                ?>
                <div class="form-group"
                     data-required="<?= ($fieldValue['MANDATORY'] == 'Y') ? 'true' : 'false' ?>"
                     data-max-size="<?= $fieldValue['SETTINGS']['MAX_ALLOWED_SIZE'] ?>"
                     data-extensions="<?= json_encode($fieldValue['SETTINGS']['EXTENSIONS']) ?>"
                    >
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
<? }; ?>
