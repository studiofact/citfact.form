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
    $entityFields = $arResult['BUILDER']['FIELDS'];
    $valueList = $arResult['REQUEST'];
    $isPost = $arResult['IS_POST'];
    ?>
    <? foreach ($entityFields as $fieldValue): ?>

        <?
        if (!empty($fieldFind) && $fieldFind != $fieldValue['NAME']) {
            continue;
        }
        ?>

        <?switch ($fieldValue['TYPE']):

            case 'input':
                ?>
                <div class="form-group">
                    <label><?= $fieldValue['LABEL'] ?></label>
                    <input type="text" class="form-control" name="<?= $fieldValue['NAME'] ?>"
                           value="<?= $valueList[$fieldValue['NAME']] ?>"/>
                </div>
                <? break; ?>

            <? case 'textarea': ?>
                <div class="form-group">
                    <label><?= $fieldValue['LABEL'] ?></label>
                    <textarea class="form-control"
                              name="<?= $fieldValue['NAME'] ?>"><?= $valueList[$fieldValue['NAME']] ?></textarea>
                </div>
                <? break; ?>

            <? case 'date': ?>
                <div class="form-group">
                    <label><?= $fieldValue['LABEL'] ?></label>
                    <div class="calendar-container">
                        <input type="text" class="form-control" name="<?= $fieldValue['NAME'] ?>"
                               value="<?= $valueList[$fieldValue['NAME']] ?>"/>
                        <span class="calendar" title="<?= GetMessage('CHOOSE_DATE') ?>"
                              onclick="BX.calendar({ node: this, field: '<?= $fieldValue['FIELD_NAME'] ?>', bTime: true, bHideTime: false });"></span>
                    </div>
                </div>
                <? break; ?>

            <? case 'select': ?>
                <div class="form-group">
                    <label><?= $fieldValue['LABEL'] ?></label>
                    <select class="form-control" name="<?= $fieldValue['NAME'] ?>">
                        <? foreach ($fieldValue['VALUE_LIST'] as $value): ?>
                            <? $selected = ($value['ID'] == $valueList[$fieldValue['NAME']]) ? 'selected="selected"' : ''; ?>
                            <option value="<?= $value['ID'] ?>" <?= $selected ?>><?= $value['VALUE'] ?></option>
                        <? endforeach; ?>
                    </select>
                </div>
                <? break; ?>

            <? case 'checkbox': ?>
                <div class="form-group">
                    <label><?= $fieldValue['LABEL'] ?></label>
                    <input type="checkbox" name="<?= $fieldValue['NAME'] ?>" />
                </div>
                <? break; ?>

            <? case 'radio': ?>
                <div class="form-group">
                    <label><?= $fieldValue['LABEL'] ?></label>
                    <input type="radio" name="<?= $fieldValue['NAME'] ?>" />
                </div>
                <? break; ?>

            <? case 'file': ?>
                <div class="form-group">
                    <label><?= $fieldValue['LABEL'] ?></label>
                    <input type="file" name="<?= $fieldValue['NAME'] ?>"/>
                </div>
                <? break; ?>

            <? endswitch; ?>

        <?
        if ($fieldFind == $fieldValue['NAME']) {
            break;
        }
        ?>

    <? endforeach; ?>
<? }; ?>
