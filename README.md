Генератор форм на основе highload инфоблоков
========
<br>
Пример использования:

	$APPLICATION->IncludeComponent('bitrix:feedback', '', array(
		"HLBLOCK_ID" => 2, // Идентификатор HL блока,
        "DISPLAY_FIELDS" => array(), // Список полей которые будут отображаться в форме
        "TEXTAREA_FIELDS" => array(), // Список полей которые будут отображаться в форме как textarea
		"EVENT_NAME" => "FEEDBACK", // Название почтового события
		"EVENT_TEMPLATE" => "", // Идентификатор почтового шаблона
		"AJAX" => "N", // Включить AJAX режим
		"USE_CAPTCHA" => "N", // Использовать каптчу
		"REDIRECT_PATH" => "" // УРЛ адрес для перенаправления после успешного добавления
	));
