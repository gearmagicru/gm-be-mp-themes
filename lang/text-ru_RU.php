<?php
/**
 * Этот файл является частью расширения модуля веб-приложения GearMagic.
 * 
 * Пакет русской локализации.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

return [
    '{name}'        => 'Темы оформления',
    '{description}' => 'Управление визуальным оформлением',
    '{permissions}' => [
        'any'  => ['Полный доступ', 'Просмотр и установки тем оформления'],
        'view' => ['Просмотр', 'Просмотр тем оформления'],
        'read' => ['Чтение', 'Просмотр тем оформления']
    ],

    // Grid: контекстное меню записи
    'Theme information' => 'Информация о теме',
    // Grid: панель инструментов
    'Edit record' => 'Редактировать',
    'You need to choose a theme' => 'Вам необходимо выбрать тему',
    // Grid: панель инструментов / Создать (create)
    'Create' => 'Создать',
    'Creating a theme based on the selected' => 'Создание темы на основе выбранной',
    // Grid: панель инструментов / Загрузить (upload)
    'Upload' => 'Загрузить',
    'Upload a theme' => 'Загрузка темы',
    // Grid: панель инструментов / Установить (install)
    'Install' => 'Установить',
    'Install a theme' => 'Установка темы',
    // Grid: панель инструментов / Удалить (uninstall)
    'Uninstall' => 'Удалить',
    'Completely delete an installed theme' => 'Полностью удаление установленной темы',
    'Are you sure you want to completely delete the installed theme?' 
        => 'Вы уверены, что хотите полностью удалить установленную тему (все файлы темы будут удалены)?',
    // Grid: панель инструментов / Удалить (delete)
    'Delete' => 'Удалить',
    'Delete an uninstalled theme from the repository' => 'Удаление не установленной темы из репозитория',
    'Are you sure you want to delete the uninstalled theme from the repository?' => 'Вы уверены, что хотите удалить не установленную тему из репозитория?',
    // Grid: панель инструментов / Демонтаж (unmount)
    'Unmount' => 'Демонтаж',
    'Delete an installed theme without removing it from the repository' => 'Демонтаж (удаление) установленной темы без удаления ёё из репозитория',
    'Are you sure you want to remove the installed theme without removing it from the repository?' 
        => 'Вы уверены, что хотите демонтировать (удалить) установленную тему без удаления его из репозитория?',
    // Grid: фильтр
    'All' => 'Все',
    // Grid: поля
    'Name' => 'Название',
    'Description' => 'Описание',
    'Identifier' => 'Идентификатор',
    'Theme directory' => 'Каталог темы',
    'Author' => 'Автор',
    'Version' => 'Версия',
    'Side' => 'Назначение',
    'License' => 'Лицензия',
    'By default' => 'По умолчанию',
    'You can assign (by default) one of the themes for: {0}, {1}' => 'Вы можете назначить (по умолчанию) одну из тем для: {0}, {1}',
    'Status' => 'Статус',
    'Keywords' => 'Ключевые слова',
    'Links to theme templates' => 'Ссылки на шаблоны темы',
    'Go to templates theme' => 'Перейти к шаблонам темы',
    // Grid: статус
    'installed' => 'установлена',
    'not installed' => 'не установлена',
    // Grid: сообшения / заголовок
    'Setting the default theme' => 'Назначение темы по умолчанию',
    'Deleting the theme' => 'Удаление темы',
    // Grid: сообшения / текст
    'The theme "{0}" is set as the default for "{1}"' => 'Тема "<b>{0}</b>" установлена, как текущая для "<b>{1}</b>".',
    'The theme "{0}" has been successfully deleted' => 'Тема "<b>{0}</b>" успешно удалена.',
    // Grid: ошибки
    'In the "By default" column, you can only select the default theme with the switch, but not disable' 
        => 'В столбце "По умолчанию" вы можете только выбрать тему по умолчанию с помощью переключателя, но не отключить.',
    'It is not possible to delete the theme "{0}", because it is installed (first dismantle it)' 
        => 'Невозможно удалить тему "{0}", т.к. она установлена (выполните сначала ёё демонтаж).',
    'The path to the theme is incorrectly specified' => 'Неправильно указан путь к теме.',
    'The theme directory "{0}" does not exist' => 'Каталог темы "{0}" не существует.',
    'Error deleting the "{0}" theme directory' => 'Ошибка удаления каталога "{0}" темы.',

    // Create
    '{create.title}' => 'Создание темы на основе "{0}"',
    // Create: поля
    'Catalog' => 'Каталог',
    'All files for the new theme will be copied to the directory specified in the "Directory" field. After adding all the files, the theme will be installed.' 
        => 'Все файлы для новой темы, будут скопированы в каталог указанный в поле "Каталог". После добавления всех файлов, тема будет установлена.',
    // Create: сообшения / заголовок
    'Create a theme' => 'Создание темы',
    // Create: сообшения / текст
    'Theme "{0}" created successfully' => 'Тема "<b>{0}</b>" успешено создана.',
    // Create: ошибки
    'The theme you have chosen "{0}" does not exist' => 'Выбранная вами тема "{0}" не существует.',
    'It is impossible to get information about the topic "{0}" (missing or error in the file "package.json")' 
        => 'Невозможно получить информацию о теме "{0}" (отсутствует или ошибка в файле "package.json").',
    'A theme with the specified directory "{0}" already exists' => 'Тема с указанным каталогом "{0}" уже существует.',
    'A theme with the specified name "{0}" already exists' => 'Тема с указанным именем "{0}" уже существует.',
    'Unable to create theme directory "{0}"' => 'Невозможно создать каталог темы "{0}".',
    'Unable to copy theme files "{0}" to directory "{1}"' => 'Невозможно выполнить копирование файлов темы "{0}" в каталог "{1}".',
    'File "{0}" theme information package "{1}" not found' => 'Файл "{0}" пакета информации темы "{1}" не найден.',
    'Unable to save package info for theme "{0}"' => 'Невозможно сохранить пакет информации для темы "{0}"',
    'Unable to create theme' => 'Невозможно создать тему.',

    // Install
    '{install.title}' => 'Установка темы "{0}"',

    // Install: сообшения / заголовок
    'Installing the theme' => 'Установка темы',
    // Install: сообшения / текст
    'Subject "{0}" successfully added' => 'Тема "<b>{0}</b>" успешно добавлена.',
    // Install: ошибки
    'Unable to install theme (error writing to file)' => 'Невозможно установить тему (ошибка записи в файл конфигурации).',
    'Your chosen theme "{0}" is already installed' => 'Выбранная вами тема "{0}" уже установлена.',

    // Unmount: сообшения / заголовок
    'Unmounting the theme' => 'Демонтаж темы',
    // Unmount: сообшения / текст
    'The theme "{0}" has been successfully unmounted' => 'Тема "<b>{0}</b>" успешно демонтирована.',
    // Unmount: ошибки
    'It is impossible to dismantle (delete) the theme "{0}", because it is the current one' 
        => 'Невозможно выполнить демонтаж (удаление) темы "{0}", т.к. она является текущей.',
    'Unable to unmount theme (error writing to file)' => 'Невозможно демонтировать тему (ошибка записи в файл конфигурации).',

    // Uninstall: ошибки
    'Unable to uninstall theme (error writing to file)' => 'Невозможно удалить тему (ошибка записи в файл конфигурации).',
    'Unable to get information for the theme "{0}"' => 'Невозможно получить информацию для темы "{0}".',

    // Package
    '{package.title}' => 'Информация о теме',
    '{package.titleTpl}' => 'Информация о теме "{name}"',
    // Package: сообшения / заголовок
    'Change information' => 'Изменение информации',
    // Package: сообшения / текст
    'Theme info updated successfully' => 'Информация о теме успешно обновлена.',
    'Theme info update error' => 'Ошибка обновления информации темы.',

    // Upload
    '{upload.title}' => 'Загрузка темы',
    // Upload: поля
    'In order to download the theme archive file, you need to select a file with the extension ".zip"' 
        => 'Для того, что загрузить файл архива темы, вам необходимо выбрать файла с расширением ".zip"',
    // Upload: сообшения / заголовок
    'Uploading a theme' => 'Загрузка темы',
    'Theme file' => 'Файл темы',
    'Upload' => 'Загрузить',
    // Upload: сообшения / текст
    'Theme uploading error' => 'Ошибка загрузки темы',
    'Theme file uploaded successfully' => 'Файл темы успешно загружен.',
    // Upload: ошибки
    'The theme directory "{0}" already exists'  => 'Каталог темы "{0}" уже существует',
    'Unable to unzip file "{0}"' => 'Невозможно разархивировать файл "{0}"'
];
