## Пользовательские события

- `onAfterBuilder` - срабатывает после сбора данных для формы
- `onBeforeStorage` - срабатывает прежде чем добавить запись в слой хранения данных
- `onAfterStorage` - срабатывает после успешной записи в слой хранения данных перед вызовом почтового события
- `onMacrosJoin` - срабатывает до объединения всех макросов в один, событие используется для своей реализации объединения макросов

## Пример использования

``` php
<?php
// init.php

use Bitrix\Main\Loader;
use Bitrix\Main\EventManager;
use Citfact\Form\Event;
use Citfact\Form\EventResult;
use Citfact\Form\FormEvents;

Loader::includeModule('citfact.form');

$eventManager = EventManager::getInstance();
$eventManager->addEventHandler(Event::MODULE_ID, FormEvents::BUILD, 'onAfterBuilderHandler');
$eventManager->addEventHandler(Event::MODULE_ID, FormEvents::PRE_STORAGE, 'onBeforeStorageHandler');
$eventManager->addEventHandler(Event::MODULE_ID, FormEvents::STORAGE, 'onAfterStorageHandler');
$eventManager->addEventHandler(Event::MODULE_ID, FormEvents::MACROS_JOIN, 'onMacrosJoin');

/**
 * @param Event $event
 * @return EventResult
 */
function onAfterBuilderHandler(Event $event)
{
    $builderData = $event->getParameters();
    $eventResult = new EventResult();

    $builderData['DEFAULT_FIELDS']['ACTIVE']['NAME'] = 'Change Active Name';
    $eventResult->modifyFields($builderData);

    return $eventResult;
}

/**
 * @param Event $event
 * @return EventResult
 */
function onBeforeStorageHandler(Event $event)
{
    $requestData = $event->getParameters();
    $eventResult = new EventResult();
    
    // Если необходим builder, для доступа к ID инфоблока или highload
    // $builder = $event->getBuilder();
    
    $requestData['NAME'] = 'Change NAME';
    $eventResult->modifyFields($requestData);

    return $eventResult;
}

/**
 * @param Event $event
 * @return EventResult
 */
function onAfterStorageHandler(Event $event)
{
    $requestData = $event->getParameters();
    $eventResult = new EventResult();

    $requestData['NAME'] = 'Change NAME second time';
    $eventResult->modifyFields($requestData);

    return $eventResult;
}

/**
 * @param Event $event
 * @return EventResult
 */
function onMacrosJoin(Event $event)
{
    $parameters = $event->getParameters();
    $builder = $event->getBuilder();

    $eventResult = new EventResult();
    $eventResult->setMacrosJoin('Change macros join');
    
    return $eventResult;
}
```