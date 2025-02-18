<?php
/**
 * Этот файл является частью расширения модуля веб-приложения GearMagic.
 * 
 * Файл конфигурации установки модуля.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c] 2015 Этот файл является частью расширения модуля веб-приложения GearMagic. Web-студия
 * @license https://gearmagic.ru/license/
 */

return [
    'id'          => 'gm.be.mp.themes',
    'moduleId'    => 'gm.be.mp',
    'name'        => 'Themes',
    'description' => 'Themes management',
    'namespace'   => 'Gm\Backend\Marketplace\Themes',
    'path'        => '/gm/gm.be.mp.themes',
    'route'       => 'themes',
    'locales'     => ['ru_RU', 'fr_FR', 'en_GB', 'be_BY'],
    'permissions' => ['any', 'read', 'info'],
    'events'      => [],
    'required'    => [
        ['php', 'version' => '8.2'],
        ['app', 'code' => 'GM MS'],
        ['app', 'code' => 'GM CMS'],
        ['app', 'code' => 'GM CRM'],
        ['module', 'id' => 'gm.be.mp']
    ]
];
