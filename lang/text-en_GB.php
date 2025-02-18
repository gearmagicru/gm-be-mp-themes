<?php
/**
 * Этот файл является частью расширения модуля веб-приложения GearMagic.
 * 
 * Пакет английской (британской) локализации.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

return [
    '{name}'        => 'Themes',
    '{description}' => 'Themes management',
    '{permissions}' => [
        'any'  => ['Full access', 'Viewing and setting themes'],
        'view' => ['View', 'View themes'],
        'read' => ['Read', 'Read themes']
    ],

    // Grid: контекстное меню записи
    'Theme information' => 'Theme information',
    // Grid: панель инструментов
    'Edit record' => 'Edit record',
    'You need to choose a theme' => 'You need to choose a theme',
    // Grid: панель инструментов / Создать (create)
    'Create' => 'Create',
    'Creating a theme based on the selected' => 'Creating a theme based on the selected',
    // Grid: панель инструментов / Загрузить (upload)
    'Upload' => 'Upload',
    'Upload a theme' => 'Upload a theme',
    // Grid: панель инструментов / Установить (install)
    'Install' => 'Install',
    'Install a theme' => 'Install a theme',
    // Grid: панель инструментов / Удалить (uninstall)
    'Uninstall' => 'Uninstall',
    'Completely delete an installed theme' => 'Completely delete an installed theme',
    'Are you sure you want to completely delete the installed theme?' 
        => 'Are you sure you want to completely delete the installed theme??',
    // Grid: панель инструментов / Удалить (delete)
    'Delete' => 'Delete',
    'Delete an uninstalled theme from the repository' => 'Delete an uninstalled theme from the repository',
    'Are you sure you want to delete the uninstalled theme from the repository?' => 'Are you sure you want to delete the uninstalled theme from the repository?',
    // Grid: панель инструментов / Демонтаж (unmount)
    'Unmount' => 'Unmount',
    'Delete an installed theme without removing it from the repository' => 'Delete an installed theme without removing it from the repository',
    'Are you sure you want to remove the installed theme without removing it from the repository?' 
        => 'Are you sure you want to remove the installed theme without removing it from the repository?',
    // Grid: фильтр
    'All' => 'All',
    // Grid: поля
    'Name' => 'Name',
    'Description' => 'Description',
    'Identifier' => 'Identifier',
    'Theme directory' => 'Theme directory',
    'Author' => 'Author',
    'Version' => 'Version',
    'Side' => 'Side',
    'License' => 'License',
    'By default' => 'By default',
    'You can assign (by default) one of the themes for: {0}, {1}' => 'You can assign (by default) one of the themes for: {0}, {1}',
    'Status' => 'Status',
    'Keywords' => 'Keywords',
    'Links to theme templates' => 'Links to theme templates',
    'Go to templates theme' => 'Go to templates theme',
    // Grid: статус
    'installed' => 'installed',
    'not installed' => 'not installed',
    // Grid: сообшения / заголовок
    'Setting the default theme' => 'Setting the default theme',
    'Deleting the theme' => 'Deleting the theme',
    // Grid: сообшения / текст
    'The theme "{0}" is set as the default for "{1}"' => 'The theme "<b>{0}</b>" is set as the default for "{1}".',
    'The theme "{0}" has been successfully deleted' => 'The theme "<b>{0}</b>" has been successfully deleted.',
    // Grid: ошибки
    'In the "By default" column, you can only select the default theme with the switch, but not disable' 
        => 'In the "By default" column, you can only select the default theme with the switch, but not disable.',
    'It is not possible to delete the theme "{0}", because it is installed (first dismantle it)' 
        => 'It is not possible to delete the theme "{0}", because it is installed (first dismantle it).',
    'The path to the theme is incorrectly specified' => 'The path to the theme is incorrectly specified.',
    'The theme directory "{0}" does not exist' => 'The theme directory "<b>{0}</b>" does not exist.',
    'Error deleting the "{0}" theme directory' => 'Error deleting the "<b>{0}</b>" theme directory.',

    // Create
    '{create.title}' => 'Create a theme based on "{0}"',
    // Create: поля
    'Catalog' => 'Catalog',
    'All files for the new theme will be copied to the directory specified in the "Directory" field. After adding all the files, the theme will be installed.' 
        => 'All files for the new theme will be copied to the directory specified in the "Directory" field. After adding all the files, the theme will be installed.',
    // Create: сообшения / заголовок
    'Create a theme' => 'Create a theme',
    // Create: сообшения / текст
    'Theme "{0}" created successfully' => 'Theme "<b>{0}</b>" created successfully.',
    // Create: ошибки
    'The theme you have chosen "{0}" does not exist' => 'The theme you have chosen "{0}" does not exist.',
    'It is impossible to get information about the topic "{0}" (missing or error in the file "package.json")' 
        => 'It is impossible to get information about the topic "{0}" (missing or error in the file "package.json").',
    'A theme with the specified directory "{0}" already exists' => 'A theme with the specified directory "{0}" already exists.',
    'A theme with the specified name "{0}" already exists' => 'A theme with the specified name "{0}" already exists.',
    'Unable to create theme directory "{0}"' => 'Unable to create theme directory "{0}".',
    'Unable to copy theme files "{0}" to directory "{1}"' => 'Unable to copy theme files "{0}" to directory "{1}".',
    'File "{0}" theme information package "{1}" not found' => 'File "{0}" theme information package "{1}" not found.',
    'Unable to save package info for theme "{0}"' => 'Unable to save package info for theme "{0}"',
    'Unable to create theme' => 'Unable to create theme.',

    // Install
    '{install.title}' => 'Installing a theme "{0}"',

    // Install: сообшения / заголовок
    'Installing the theme' => 'Installing a theme',
    // Install: сообшения / текст
    'Subject "{0}" successfully added' => 'Subject "<b>{0}</b>" successfully added.',
    // Install: ошибки
    'Unable to install theme (error writing to file)' => 'Unable to install theme (error writing to file).',
    'Your chosen theme "{0}" is already installed' => 'Your chosen theme "{0}" is already installed.',

    // Unmount: сообшения / заголовок
    'Unmounting the theme' => 'Unmounting the theme',
    // Unmount: сообшения / текст
    'The theme "{0}" has been successfully unmounted' => 'The theme "<b>{0}</b>" has been successfully unmounted.',
    // Unmount: ошибки
    'It is impossible to dismantle (delete) the theme "{0}", because it is the current one' 
        => 'It is impossible to dismantle (delete) the theme "{0}", because it is the current one.',
    'Unable to unmount theme (error writing to file)' => 'Unable to unmount theme (error writing to file).',

    // Uninstall: ошибки
    'Unable to uninstall theme (error writing to file)' => 'Unable to uninstall theme (error writing to file).',
    'Unable to get information for the theme "{0}"' => 'Unable to get information for the theme "{0}".',

    // Package
    '{package.title}' => 'Theme information',
    '{package.titleTpl}' => 'Theme information "{name}"',
    // Package: сообшения / текст
    'Change information' => 'Change information',
    // Package: сообшения / текст
    'Theme info updated successfully' => 'Theme info updated successfully.',
    'Theme info update error' => 'Theme info update error.',

    // Upload
    '{upload.title}' => 'Uploading a theme',
    // Upload: поля
    'In order to download the theme archive file, you need to select a file with the extension ".zip"' 
        => 'In order to download the theme archive file, you need to select a file with the extension ".zip"',
    // Upload: сообшения / заголовок
    'Uploading a theme' => 'Uploading a theme',
    'Theme file' => 'Theme file',
    'Upload' => 'Upload',
    // Upload: сообшения / текст
    'Theme uploading error' => 'Theme uploading error',
    'Theme file uploaded successfully' => 'Theme file uploaded successfully.',
    // Upload: ошибки
    'The theme directory "{0}" already exists'  => 'The theme directory "{0}" already exists',
    'Unable to unzip file "{0}"' => 'Unable to unzip file "{0}"'
];
