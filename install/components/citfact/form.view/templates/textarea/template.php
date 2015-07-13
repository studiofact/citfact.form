<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$this->setFrameMode(true);
?>

<div class="form-group">
    <label><?= $arResult['LABEL'] ?></label>
    <textarea class="form-control" name="<?= $arResult['NAME'] ?>"><?= $arResult['VALUE'] ?></textarea>
</div>
