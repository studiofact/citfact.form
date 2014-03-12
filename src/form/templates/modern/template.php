<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$path = sprintf('%s%s', getenv('DOCUMENT_ROOT'), $templateFolder);
if (file_exists($path . '/user_type.php')) {
    include $path . '/user_type.php';
}

if ($arParams['AJAX'] == 'Y') {
    $APPLICATION->AddHeadScript($templateFolder . '/script.js');
}

$APPLICATION->SetAdditionalCSS($templateFolder . '/style.css');
?>

<div class="form-container">
    <? if ($arResult['SUCCESS'] === true): ?>
        <div class="alert alert-success"><?= GetMessage('SUCCESS_MESSAGE') ?></div>
    <? endif; ?>

    <? if ($arParams['AJAX'] == 'Y'): ?>
        <div class="alert alert-success hidden"><?= GetMessage('SUCCESS_MESSAGE') ?></div>
    <? endif; ?>

    <? if (sizeof($arResult['ERRORS']) > 0): ?>
        <div class="alert alert-danger">
            <ul>
                <? foreach ($arResult['ERRORS'] as $nameFiled => $value): ?>
                    <li><?= $value ?></li>
                <? endforeach; ?>
            </ul>
        </div>
    <? endif; ?>

    <form action="<?= POST_FORM_ACTION_URI ?>" method="post" enctype="multipart/form-data"
          id="form-feedback-<?= $arResult['FORM']['COMPONENT_ID'] ?>">
        <? $userTypePrint($arResult); ?>
        <? if ($arParams['USE_CAPTCHA'] == 'Y'): ?>
            <div class="field margin-top">
                <div class="captcha-container">
                    <img class="captcha-image"
                         src="/bitrix/tools/captcha.php?captcha_sid=<?= $arResult['CAPTCHA_CODE'] ?>" alt="captcha"
                         width="110" height="33"/>
                    <a href="#" class="captcha-reload"><?= GetMessage('CAPTCHA_RELOAD') ?></a>
                </div>
                <input type="hidden" name="captcha_sid" value="<?= $arResult['CAPTCHA_CODE'] ?>"/>
                <input type="text" class="form-control validation empty" name="captcha_word"
                       value="<?= $arResult['FORM']['CAPTCHA'] ?>"/>

                <div class="clear"></div>
            </div>
        <? endif; ?>
        <? if ($arParams['AJAX'] == 'Y'): ?>
            <input type="hidden" name="ajax_id" value="<?= $arResult['FORM']['COMPONENT_ID'] ?>"/>
        <? endif; ?>
        <input type="submit" class="submit-form" name="send_form_<?= $arResult['FORM']['COMPONENT_ID'] ?>"
               value="<?= GetMessage('FILED_SUBMIT') ?>"/>

        <div class="clear"></div>
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        var
            feedback = new Feedback('#form-feedback-<?=$arResult['FORM']['COMPONENT_ID']?>'),
            feedbackForm = $('#form-feedback-<?=$arResult['FORM']['COMPONENT_ID']?>');

        feedbackForm.on('click', '.captcha-reload', function () {
            feedback.reloadCaptcha();
            return false;
        });

        <?if($arParams['AJAX'] == 'Y'):?>
        var formValidation = {
            errors: [],

            validate: function () {
                var self = this;
                feedbackForm.find('input.validation, textarea.validation').each(function () {
                    var input = $(this),
                        value = input.val();

                    if (input.is('.email')) {
                        if (!value.match(/^[а-яА-Я\.\-\w]+@[a-zA-Zа-яА-Я_]+?\.([a-zA-Z]{2,4}|рф)$/)) {
                            self.errors.push(input);
                        }
                    }

                    if (input.is('.phone')) {
                        if (!value.match(/^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$/)) {
                            self.errors.push(input);
                        }
                    }

                    if (input.is('.empty')) {
                        if (value == '') {
                            self.errors.push(input);
                        }
                    }
                });
            },

            getErrors: function () {
                return this.errors;
            },

            flushErrors: function () {
                this.errors = [];
            }
        };

        feedbackForm.submit(function () {
            formValidation.validate();
            if (formValidation.getErrors().length > 0) {
                var errorList = formValidation.getErrors();
                for (key in errorList) {
                    var currItem = errorList[key];
                    currItem.addClass('has-error');
                }

                setTimeout(function () {
                    feedbackForm.find('input.validation, textarea.validation').removeClass('has-error');
                }, 2000);

                formValidation.flushErrors();
            } else {
                var inputSubmit = feedbackForm.find('input[type=submit]');
                inputSubmit.prop('disabled', 'disabled');
                feedback.submitForm(function (response) {
                    inputSubmit.prop('disabled', '');
                    errorsArr = $.makeArray(response.errors);
                    if (errorsArr.length > 0) {
                        for (key in response.errors) {
                            var currItem = $('input[name=' + key + '], textarea[name=' + key + ']').eq(0);
                            currItem.addClass('has-error');
                        }

                        setTimeout(function () {
                            feedbackForm.find('input.validation, textarea.validation').removeClass('has-error');
                        }, 2000);
                    } else {
                        feedbackForm.parent().find('.alert-success').removeClass('hidden');
                        feedbackForm.trigger('reset');

                        setTimeout(function () {
                            feedbackForm.parent().find('.alert-success').addClass('hidden');
                        }, 4000);
                    }
                });
            }

            return false;
        });
        <?endif;?>
    });
</script>