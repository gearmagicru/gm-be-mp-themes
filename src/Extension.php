<?php
/**
 * Расширение модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Marketplace\Themes;

/**
 * Расширение "Темы оформления".
 * 
 * Расширение принадлежит модулю "Маркетплейс".
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Marketplace\Themes
 * @since 1.0
 */
class Extension extends \Gm\Panel\Extension\Extension
{
    /**
     * {@inheritdoc}
     */
    public string $id = 'gm.be.mp.themes';

    /**
     * {@inheritdoc}
     */
    public string $defaultController = 'grid';

    /**
     * {@inheritdoc}
     */
    public function controllerMap(): array
    {
        return [
            'create'  => 'CreateForm',
            'install' => 'InstallForm',
            'package' => 'PackageForm',
            'upload'  => 'UploadForm',
        ];
    }
}