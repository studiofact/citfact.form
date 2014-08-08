## Установка через composer

Используйте composer для управления зависимостями и установкой модуля

``` bash
php composer.phar require citfact/form
```

Вам будет предложено набрать версию модуля

``` bash
  # 0.5.3-RC stable
  Please provide a version constraint for the citfact/form requirement: 0.5.3-RC
```
  
## Альтернативная установка

``` bash
# Переходим в корень проекта
cd /path/to/project/

# Клонируем репозиторий
git clone https://github.com/studiofact/citfact.form.git

# Cоздадим папку local/modules и переносим туда модуль
mkdir local && mkdir local/modules
cp -rf citfact.form local/modules

rm -rf citfact.form
```

Далее в административной панели в разделе "Marketplace > Установленные решения" устанавливаем модуль.  
