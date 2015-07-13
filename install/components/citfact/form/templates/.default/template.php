<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$this->setFrameMode(true);

if (!$arResult['IS_AJAX']) {
    $GLOBALS['APPLICATION']->AddHeadScript($templateFolder . '/script.js');
    $GLOBALS['APPLICATION']->SetAdditionalCSS($templateFolder . '/style.css');
}
?>

<form name="<?= $arResult['FORM_NAME'] ?>" action="<?= POST_FORM_ACTION_URI ?>" method="post"
      enctype="multipart/form-data" class="form-generator"
      id="form-container">

    <?php if ($arResult['SUCCESS'] === true): ?>
        <div class="alert alert-success"><?= GetMessage('SUCCESS_MESSAGE') ?></div>
    <?php endif; ?>

    <?php if (sizeof($arResult['ERRORS']['LIST']) > 0): ?>
        <div class="alert alert-danger">
            <?php foreach ($arResult['ERRORS']['LIST'] as $type => $value): ?>
                <?php if ($type == 'CAPTCHA' || $type == 'CSRF'): ?>
                    <div><?= GetMessage($value) ?></div>
                <?php else: ?>
                    <div><?= $value ?></div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php foreach ($arResult['VIEW'] as $field): ?>
        <?php $APPLICATION->IncludeComponent('citfact:form.view', $field['TYPE'], $field, false); ?>
    <?php endforeach; ?>

    <?php if ($arParams['USE_CAPTCHA'] == 'Y'): ?>
        <div class="form-group" data-required="true">
            <label><?= GetMessage('CAPTCHA_LABEL') ?></label>
            <img class="captcha-image captcha-reload"
                 src="/bitrix/tools/captcha.php?captcha_sid=<?= $arResult['CAPTCHA'] ?>"
                 alt="captcha"
                 width="110"
                 height="33"
                />
            <input type="hidden" name="<?= $arResult['FORM_NAME'] ?>[CAPTCHA_TOKEN]"
                   value="<?= $arResult['CAPTCHA'] ?> "/>
            <input type="text" class="form-control" name="<?= $arResult['FORM_NAME'] ?>[CAPTCHA]"
                   value="<?= $arResult['FORM']['CAPTCHA'] ?>"/>
        </div>
    <?php endif; ?>

    <?php if ($arParams['USE_CSRF'] == 'Y'): ?>
        <input type="hidden" name="<?= $arResult['FORM_NAME'] ?>[CSRF]" value="<?= $arResult['CSRF'] ?>"/>
    <?php endif; ?>

    <input type="submit"
           data-send-text="<?= GetMessage('FILED_SEND_SUBMIT') ?>"
           data-default-text="<?= GetMessage('FILED_SUBMIT') ?>"
           value="<?= GetMessage('FILED_SUBMIT') ?>"/>
</form>

<?php if (!$arResult['IS_AJAX']): ?>
    <script type="text/javascript">
        var formGenerator = new FormGenerator({
            formContainer: '#form-container',
            ajaxMode: <?= ($arParams['AJAX'] == 'Y') ? 'true' : 'false' ?>,
            captchaImg: '.captcha-image',
            captchaReload: '.captcha-reload',
            uri: '<?= POST_FORM_ACTION_URI ?>'
        });
        formGenerator.init();
    </script>
<?php endif; ?>
