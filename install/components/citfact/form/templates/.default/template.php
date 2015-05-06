<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

if (!$arResult['IS_AJAX']) {
    $GLOBALS['APPLICATION']->AddHeadScript($templateFolder . '/script.js');
    $GLOBALS['APPLICATION']->SetAdditionalCSS($templateFolder . '/style.css');
}
?>

<form name="<?= $arResult['FORM_NAME'] ?>" action="<?= POST_FORM_ACTION_URI ?>" method="post"
      enctype="multipart/form-data" class="form-generator"
      id="form-container">

    <? if ($arResult['SUCCESS'] === true): ?>
        <div class="alert alert-success"><?= GetMessage('SUCCESS_MESSAGE') ?></div>
    <? endif; ?>

    <? if (sizeof($arResult['ERRORS']['LIST']) > 0): ?>
        <div class="alert alert-danger">
            <? foreach ($arResult['ERRORS']['LIST'] as $type => $value): ?>
                <? if ($type == 'CAPTCHA' || $type == 'CSRF'): ?>
                    <div><?= GetMessage($value) ?></div>
                <? else: ?>
                    <div><?= $value ?></div>
                <? endif; ?>
            <? endforeach; ?>
        </div>
    <? endif; ?>

    <? foreach($arResult['VIEW'] as $field): ?>
        <? $APPLICATION->IncludeComponent('citfact:form.view', $field['TYPE'], $field, false); ?>
    <? endforeach; ?>

    <? if ($arParams['USE_CAPTCHA'] == 'Y'): ?>
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
    <? endif; ?>

    <? if ($arParams['USE_CSRF'] == 'Y'): ?>
        <input type="hidden" name="<?= $arResult['FORM_NAME'] ?>[CSRF]" value="<?= $arResult['CSRF'] ?>"/>
    <? endif; ?>

    <input type="submit"
           data-send-text="<?= GetMessage('FILED_SEND_SUBMIT') ?>"
           data-default-text="<?= GetMessage('FILED_SUBMIT') ?>"
           value="<?= GetMessage('FILED_SUBMIT') ?>"/>
</form>

<? if (!$arResult['IS_AJAX']): ?>
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
<? endif; ?>
