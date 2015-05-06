## Установка через composer

Используйте composer для управления зависимостями и установкой модуля

``` bash
php composer.phar require citfact/form
```

Вам будет предложено набрать версию модуля

``` bash
  Please provide a version constraint for the citfact/form requirement: dev-master
```

Подключите composer автолоадер 
``` php
// init.php

require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
```

Далее в административной панели в разделе "Marketplace > Установленные решения" устанавливаем модуль.  
