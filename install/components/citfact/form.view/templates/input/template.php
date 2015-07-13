<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$this->setFrameMode(true);
?>

<div class="form-group">
    <label><?= $arResult['LABEL'] ?></label>
    <input type="text" class="form-control" name="<?= $arResult['NAME'] ?>" value="<?= $arResult['VALUE'] ?>"/>
</div>
