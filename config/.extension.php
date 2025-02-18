<?php
/**
 * Этот файл является частью расширения модуля веб-приложения GearMagic.
 * 
 * Файл конфигурации модуля.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c] 2015 Этот файл является частью расширения модуля веб-приложения GearMagic. Web-студия
 * @license https://gearmagic.ru/license/
 */

return [
    'translator' => [
        'locale'   => 'auto',
        'patterns' => [
            'text' => [
                'basePath' => __DIR__ . '/../lang',
                'pattern'  => 'text-%s.php'
            ]
        ],
        'autoload' => ['text'],
        'external' => [BACKEND]
    ],

    'accessRules' => [
        // для авторизованных пользователей Панели управления
        [ // разрешение "Полный доступ" (any: view, read)
            'allow',
            'permission'  => 'any',
            'controllers' => [
                'Trigger'     => ['combo'],
                'Grid'        => ['data', 'view', 'update', 'delete', 'filter'],
                'CreateForm'  => ['view', 'complete'],
                'InstallForm' => ['view', 'complete'],
                'PackageForm' => ['data', 'view', 'update'],
                'UploadForm'  => ['view', 'complete'],
                'Uninstall'   => ['complete'],
                'Unmount'     => ['complete'],
                'Search'      => ['data', 'view']
            ],
            'users' => ['@backend']
        ],
        [ // разрешение "Просмотр" (view)
            'allow',
            'permission'  => 'view',
            'controllers' => [
                'Trigger' => ['combo'],
                'Grid'    => ['data', 'view', 'filter'],
                'Search'  => ['data', 'view']
            ],
            'users' => ['@backend']
        ],
        [ // разрешение "Чтение" (read)
            'allow',
            'permission'  => 'read',
            'controllers' => [
                'Trigger'     => ['combo'],
                'Grid'        => ['data', 'view', 'filter'],
                'Form'        => ['data', 'view'],
                'PackageForm' => ['data'],
                'Search'      => ['data']
            ],
            'users' => ['@backend']
        ],
        [ // разрешение "Информация о модуле" (info)
            'allow',
            'permission'  => 'info',
            'controllers' => ['Info'],
            'users'       => ['@backend']
        ],
        [ // для всех остальных, доступа нет
            'deny'
        ]
    ],

    'viewManager' => [
        'id'       => 'gm-mp-themes-{name}',
        'useTheme' => true,
        'viewMap'  => [
            // информация о расширении
            'info' => [
                'viewFile'      => '//backend/extension-info.phtml', 
                'forceLocalize' => true
            ],
        ]
    ]
];
