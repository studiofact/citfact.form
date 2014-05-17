<?php

/*
 * This file is part of the Studio Fact package.
 *
 * (c) Kulichkin Denis (onEXHovia) <onexhovia@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once getenv('DOCUMENT_ROOT') . '/bitrix/modules/main/include/prolog_admin_before.php';

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Highloadblock as HL;

Loc::loadMessages(__FILE__);

global $APPLICATION, $USER_FIELD_MANAGER;

$application = Application::getInstance();
$applicationOld = & $APPLICATION;

$applicationOld->setTitle(Loc::getMessage('HLBLOCK_TEMPLATE_TITLE'));

if (!Loader::includeModule('citfact.form')) {
    $applicationOld->authForm(Loc::getMessage('ACCESS_DENIED'));
}

if (!Loader::includeModule('highloadblock')) {
    return ShowError('Module "Highloadblock" not set');
}

$request = $application
    ->getContext()
    ->getRequest();

$includePath = array(
    'prolog' => '/bitrix/modules/main/include/prolog_admin_after.php',
    'prolog_js' => '/bitrix/modules/main/include/prolog_admin_js.php',
    'epilog' => '/bitrix/modules/main/include/epilog_admin.php',
    'epilog_js' => '/bitrix/modules/main/include/epilog_admin_js.php',
);

$tabsStructur = array(
    array(
        'DIV' => 'group',
        'ICON' => 'main_user_edit',
        'TAB' => Loc::getMessage('HLBLOCK_TEMPLATE_TAB'),
        'TITLE' => Loc::getMessage('HLBLOCK_TEMPLATE_TAB_NAME')
    ),
);

$hlblockList = array();
$hlblockListResult = HL\HighloadBlockTable::getList(array('select' => array('ID', 'NAME')));
while ($item = $hlblockListResult->fetch()) {
    $hlblockList[$item['ID']] = array(
        'ID' => $item['ID'],
        'NAME' => sprintf('[%d] %s', $item['ID'], $item['NAME'])
    );
}

$eventTypeList = array();
$eventType = CEventType::GetList(array('LID' => SITE_ID));
while ($item = $eventType->GetNext()) {
    $eventTypeList[$item['EVENT_NAME']] = sprintf('[%s] %s', $item['EVENT_NAME'], $item['NAME']);
}

if ($request->isPost() && check_bitrix_sessid()) {
    $postData = array_map('strip_tags', $request->getPostList()->toArray());
    $postData['lid'] = $LID;

    if (empty($postData['lid'])) {
        $errorsList[] = Loc::getMessage('HLBLOCK_TEMPLATE_ERROR_SITE');
    }

    if (!array_key_exists($postData['hlblock_type'], $hlblockList)) {
        $errorsList[] = Loc::getMessage('HLBLOCK_TEMPLATE_ERROR_TYPE');
    }

    if (!isset($eventTypeList[$postData['event_type']])) {
        $errorsList[] = Loc::getMessage('HLBLOCK_TEMPLATE_ERROR_EVENT_TYPE');
    }

    if (!preg_match('/^#[a-zA-Z0-9_]+#$/', $postData['email_from']) && !filter_var($postData['email_from'], FILTER_VALIDATE_EMAIL)) {
        $errorsList[] = Loc::getMessage('HLBLOCK_TEMPLATE_ERROR_FROM');
    }

    if (!preg_match('/^#[a-zA-Z0-9_]+#$/', $postData['email_to']) && !filter_var($postData['email_to'], FILTER_VALIDATE_EMAIL)) {
        $errorsList[] = Loc::getMessage('HLBLOCK_TEMPLATE_ERROR_TO');
    }

    if (!is_array($errorsList)) {
        $eventMessage = new CEventMessage;
        $hlBlockFeilds = $USER_FIELD_MANAGER->GetUserFields(sprintf('HLBLOCK_%d', $postData['hlblock_type']), 0, LANGUAGE_ID);

        $message = null;
        foreach ($hlBlockFeilds as $name => $feild) {
            $message .= sprintf("<strong>%s</strong>: #%s#</br>\n", $feild['LIST_COLUMN_LABEL'], $name);
        }

        $eventData = array(
            'ACTIVE'     => 'Y',
            'LID'        => $postData['lid'],
            'EVENT_NAME' => $postData['event_type'],
            'EMAIL_FROM' => $postData['email_from'],
            'EMAIL_TO'   => $postData['email_to'],
            'MESSAGE'    => $message,
            'BODY_TYPE'  => 'html',
        );

        $id = $eventMessage->add($eventData);
        LocalRedirect(sprintf('%s/admin/message_edit.php?lang=%s&ID=%d', BX_ROOT, LANGUAGE_ID, $id));
    }
}

$errorsList = (isset($errorsList)) ? $errorsList : array();
$postData = (isset($postData)) ? $postData : array();
$tabControl = new CAdminTabControl('tabControl', $tabsStructur);

$formData = array();
$formControl = array('lid', 'email_from', 'email_to', 'hlblock_type', 'event_type');
foreach ($formControl as $control) {
    if (array_key_exists($control, $postData)) {
        $formData[$control] = ($control == 'lid')
            ? $postData[$control]
            : htmlspecialchars($postData[$control]);
    } else {
        $value = '';
        switch ($control) {
            case 'lid':
                $value = array();
                break;

            case 'email_from':
            case 'email_to':
                $value = '#DEFAULT_EMAIL_FROM#';
                break;
        }

        $formData[$control] = $value;
    }
}

$prologType = ($request->getQuery('mode') == 'list') ? 'prolog_js' : 'prolog';
require sprintf('%s%s', getenv('DOCUMENT_ROOT'), $includePath[$prologType]);

if (sizeof($errorsList) > 0) {
    CAdminMessage::ShowMessage(join(PHP_EOL, $errorsList));
}

?>
    <form method="post" action="<?= $applicationOld->getCurPage() ?>" enctype="multipart/form-data">
    <input type="hidden" name="lang" value="<?= LANGUAGE_ID ?>" />
    <?= bitrix_sessid_post(); ?>
<?

$tabControl->begin();
$tabControl->beginNextTab();

?>
    <tr class="heading">
        <td colspan="2"><?= Loc::getMessage('HLBLOCK_TEMPLATE_HELP') ?></td>
    </tr>
    <tr>
        <td><strong><?= Loc::getMessage('HLBLOCK_TEMPLATE_SITE') ?>:</strong></td>
        <td><?= CLang::SelectBoxMulti('LID', $formData['lid']); ?></td>
    </tr>
    <tr>
        <td><strong><?= Loc::getMessage('HLBLOCK_TEMPLATE_FROM') ?>:</strong></td>
        <td><input type="text" name="email_from" value="<?= $formData['email_from'] ?>" /></td>
    </tr>
    <tr>
        <td><strong><?= Loc::getMessage('HLBLOCK_TEMPLATE_TO') ?>:</strong></td>
        <td><input type="text" name="email_to" value="<?= $formData['email_to'] ?>" /></td>
    </tr>
    <tr>
        <td><strong><?= Loc::getMessage('HLBLOCK_TEMPLATE_TYPE') ?>:</strong></td>
        <td>
            <select name="hlblock_type">
                <? foreach($hlblockList as $hlblock): ?>
                    <? $selected = ($hlblock['ID'] == $formData['hlblock_type']) ? 'selected="selected"' : ''; ?>
                    <option <?= $selected ?> value="<?= $hlblock['ID'] ?>"><?= $hlblock['NAME'] ?></option>
                <? endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <td><strong><?= Loc::getMessage('HLBLOCK_TEMPLATE_EVENT_TYPE') ?>:</strong></td>
        <td>
            <select name="event_type" style="width: 215px;">
                <? foreach($eventTypeList as $eventTypeCode => $eventTypeName): ?>
                    <? $selected = ($eventTypeCode == $formData['event_type']) ? 'selected="selected"' : ''; ?>
                    <option <?= $selected ?> value="<?= $eventTypeCode ?>"><?= $eventTypeName ?></option>
                <? endforeach; ?>
            </select>
        </td>
    </tr>
<?

$tabControl->buttons();

?>
    <input type="submit" name="save_template" value="<?=Loc::getMessage('HLBLOCK_TEMPLATE_SAVE')?>" class="adm-btn-save">
<?

$tabControl->end();

?>
    </form>
<?

$epilogType = ($request->getQuery('mode') == 'list') ? 'epilog_js' : 'epilog';
require sprintf('%s%s', getenv('DOCUMENT_ROOT'), $includePath[$epilogType]);