<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$this->setFrameMode(true);
?>

<div class="form-group">
    <label><?= $arResult['LABEL'] ?></label>
    <?php $multiple = ($arResult['MULTIPLE'] == 'Y') ? 'multiple="multiple"' : ''  ?>
    <select class="form-control" name="<?= $arResult['NAME'] ?>" <?= $multiple ?>>
        <?php foreach ($arResult['VALUE_LIST'] as $value): ?>
            <?php $selected = ($arResult['MULTIPLE'] == 'Y')
                ? (in_array($value['ID'], $arResult['VALUE'])) ? 'selected="selected"' : ''
                : ($value['ID'] == $arResult['VALUE']) ? 'selected="selected"' : '';
            ?>
            <option value="<?= $value['ID'] ?>" <?= $selected ?>><?= $value['VALUE'] ?></option>
        <?php endforeach; ?>
    </select>
</div>
