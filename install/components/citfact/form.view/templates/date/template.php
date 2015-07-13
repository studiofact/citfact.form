<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$this->setFrameMode(true);
CJSCore::Init(array('date'));
?>

<div class="form-group">
    <label><?= $arResult['LABEL'] ?></label>
    <div class="calendar-container">
        <input type="text" class="form-control" name="<?= $arResult['NAME'] ?>" value="<?= $arResult['VALUE'] ?>"/>
        <span class="calendar" onclick="BX.calendar({ node: this, field: '<?= $arResult['NAME'] ?>' });"></span>
    </div>
</div>
