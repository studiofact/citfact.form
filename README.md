Генератор форм
========

## Описание
Генератор форм основан на highload инфоблоках, т.е пользовательские поля инфоблока выступают в роле контролов формы.
На данный момент поддерживаются пользовательские поля:
 - Строка
 - Целое число
 - Число
 - Дата/Время
 - Да/Нет
 - Файл
 - Список
 - Привязка к разделам инф. блоков
 - Привяка к элементам инф. блоков
 
## Установка:

``` bash
# Переходим в корень проекта
cd /path/to/project/

# Клонируем репозиторий
git clone https://github.com/studiofact/form.git

# Если bitrix >= 14, то создадим папку local/modules и переносим туда модуль
mkdir local && mkdir local/modules
mv form/src/citfact.form local/modules

# Если версия < 14, переносим в пространство bitrix
mv form/src/citfact.form bitrix/modules

# Удаляем репозиторий
rm -rf form
```

Далее в административной панели в разделе "Marketplace > Установленные решения" устанавливаем модуль.

## Пример использования:

``` php
$APPLICATION->IncludeComponent('citfact:form', '', array(
	"HLBLOCK_ID" => 2, // Идентификатор HL блока,
    "DISPLAY_FIELDS" => array(), // Список полей которые будут отображаться в форме
    "TEXTAREA_FIELDS" => array(), // Список полей которые будут отображаться в форме как textarea
	"EVENT_NAME" => "FEEDBACK", // Название почтового события
	"EVENT_TEMPLATE" => "", // Идентификатор почтового шаблона
	"AJAX" => "N", // Включить AJAX режим
	"USE_CAPTCHA" => "N", // Использовать каптчу
	"REDIRECT_PATH" => "" // УРЛ адрес для перенаправления после успешного добавления
));
```

Пользовательские события
 - `onBeforeHighElementAdd` - срабатывает до добавления записи в highload инфоблок
 - `onAfterHighElementAdd` - срабатывает после успешной записи в highload инфоблок до вызова почтового события

``` php
// init.php

AddEventHandler('citfact.form', 'onBeforeHighElementAdd', 'onBeforeHandler');
AddEventHandler('citfact.form', 'onAfterHighElementAdd', 'onAfterHandler');

/**
 * @param array $postData Массив с данными(передается по ссылке)
 * @param array $highLoadBlockData Информация о инфоблоке
 */
function onBeforeHandler($postData, $highLoadBlockData)
{
	...
}

/**
 * @param int $id Идентификатор добавленного элемента
 * @param array $postData Массив с данными(передается по ссылке)
 * @param array $highLoadBlockData Информация о инфоблоке
 */
function onAfterHandler($id, $postData, $highLoadBlockData)
{
	...
}
```
