<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$path = sprintf('%s%s', getenv('DOCUMENT_ROOT'), $templateFolder);
if (file_exists($path . '/user_type.php')) {
    include $path . '/user_type.php';
}

if (!$arResult['IS_AJAX']) {
    $APPLICATION->AddHeadScript($templateFolder . '/script.js');
    $APPLICATION->SetAdditionalCSS($templateFolder . '/style.css');
}
?>

    <form action="<?= POST_FORM_ACTION_URI ?>" method="post" enctype="multipart/form-data" class="form-generator"
          id="form-container-<?= $arResult['FORM']['COMPONENT_ID'] ?>">

        <? if ($arResult['SUCCESS'] === true): ?>
            <div class="alert alert-success"><?= GetMessage('SUCCESS_MESSAGE') ?></div>
        <? endif; ?>

        <? if (sizeof($arResult['ERRORS']) > 0): ?>
            <div class="alert alert-danger">
                <? foreach ($arResult['ERRORS'] as $value): ?>
                    <div><?= $value ?></div>
                <? endforeach; ?>
            </div>
        <? endif; ?>

        <? $userTypePrint($arResult); ?>
        <? if ($arParams['USE_CAPTCHA'] == 'Y'): ?>
            <div class="form-group" data-required="true">
                <label><?= GetMessage('CAPTCHA_LABEL') ?></label>
                <img class="captcha-image captcha-reload"
                     src="/bitrix/tools/captcha.php?captcha_sid=<?= $arResult['CAPTCHA_CODE'] ?>"
                     alt="captcha"
                     width="110"
                     height="33"
                    />
                <input type="hidden" name="captcha_sid" value="<?= $arResult['CAPTCHA_CODE'] ?> "/>
                <input type="text" class="form-control" name="captcha_word"
                       value="<?= $arResult['FORM']['CAPTCHA'] ?>"/>
            </div>
        <? endif; ?>

        <input type="hidden" name="component_id" value="<?= $arResult['FORM']['COMPONENT_ID'] ?>"/>
        <input type="submit"
               name="send_form"
               data-send-text="<?= GetMessage('FILED_SEND_SUBMIT') ?>"
               data-default-text="<?= GetMessage('FILED_SUBMIT') ?>"
               value="<?= GetMessage('FILED_SUBMIT') ?>"/>
    </form>

<? if (!$arResult['IS_AJAX']): ?>
    <script type="text/javascript">
        var formGenerator = new FormGenerator({
            formContainer: '#form-container-<?=$arResult['FORM']['COMPONENT_ID']?>',
            ajaxMode: <?= ($arParams['AJAX'] == 'Y') ? 'true' : 'false' ?>,
            captchaImg: '.captcha-image',
            captchaReload: '.captcha-reload',
            uri: '<?= POST_FORM_ACTION_URI ?>'
        });
        formGenerator.init();
    </script>
<? endif; ?>
