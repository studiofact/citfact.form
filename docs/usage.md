## Пример использования

``` php
$APPLICATION->IncludeComponent("citfact:form", "", array(
    "ID" => "2",
    "EVENT_NAME" => "FEEDBACK",
    "EVENT_TEMPLATE" => "",
    "EVENT_TYPE" => "",
    "BUILDER" => "",
    "STORAGE" => "",
    "VALIDATOR" => "",
    "AJAX" => "N",
    "USE_CAPTCHA" => "Y",
    "REDIRECT_PATH" => ""
    "ALIAS_FIELDS" => array("NAME" => "NameAlias", "CODE" => "CodeAlias"),
    "DISPLAY_FIELDS" => array("NAME", "UF_NAME"),
    ),
    false
);
```

## Описание параметров

* **ID** - Идентификатор HL блока или обычного инфоблока
* **EVENT_NAME** - Название почтового события
* **EVENT_TEMPLATE** - Идентификатор почтового шаблона
* **EVENT_TYPE** - Тип почтового события, при значение "IMMEDIATE" используется метод CEvent::SendImmediate иначе CEvent::Send
* **BUILDER** - Класс реализующий интерфейс ``Citfact\Form\FormBuilderInterface``, который в дальнейшем используется для генерации веб-формы, по умолчанию ``Citfact\Form\Builder\UserFieldBuilder``
* **STORAGE** - Класс реализующий интерфейс ``Citfact\Form\StorageInterface``, который в дальнейшем используется для сохранения данных веб-формы, по умолчанию ``Citfact\Form\Storage\HighLoadBlockStorage``
* **VALIDATOR** - Класс реализующий интерфейс ``Citfact\Form\FormValidatorInterface``, который используется для валидации данных веб-формы, по умолчанию ``Citfact\Form\Validator\UserFieldValidator``
* **AJAX** -  Включить AJAX режим
* **USE_CAPTCHA** - Использовать каптчу
* **ALIAS_FIELDS** - Словарь для замены стандартных наименований полей
* **DISPLAY_FIELDS** - Словарь со списком полей которые нужно отобразить, если пустой отображаются все
