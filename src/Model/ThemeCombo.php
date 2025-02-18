<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

 namespace Gm\Backend\Marketplace\Themes\Model;

use Gm;
use Gm\Helper\Str;
use Gm\Theme\Theme;
use Gm\Mvc\Module\BaseModule;
use Gm\Panel\Data\Model\Combo\ComboModel;

/**
 * Модель данных выпадающего списка тем.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Marketplace\Themes\Model
 * @since 1.0
 */
class ThemeCombo extends ComboModel
{
    /**
     * {@inheritdoc}
     * 
     * @var BaseModule|\Gm\Backend\Marketplace\Themes\Extension
     */
    public BaseModule $module;

    /**
     * Значок (отсутствующий) темы.
     *
     * @var string
     */
    protected string $thumbNone;

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this->thumbNone = $this->module->getAssetsUrl() . '/images/icon-none.svg';
    }

    /**
     * Возвращает список тем.
     *
     * @param string $side Сторона: FRONTEND, BACKEND.
     * @param Theme $theme Тема.
     * @param string $status Назначение темы.
     * 
     * @return array
     */
    protected function getThemes(string $side, Theme $theme, string $status): array
    {
        $rows = [];
        foreach ($theme->available as $name => $params) {
            /** @var null|\Gm\Theme\ThemePackage $package */
            $package = $theme->getPackage($name);
            /** @var null|array $info Информация о пакете */
            $info = $package->getInfo();
            if ($info) {
                $rows[] = [
                    'id'          => $side . '::' . $name,
                    'name'        => $name,
                    'description' => Str::ellipsis($info['description'] ?? '', 0, 90),
                    'thumb'       => $theme->getThumbUrl($name) ?: $this->thumbNone,
                    'status'      => $status,
                    'subname'     => $name === $theme->default ? '<span>(' . $this->t('active') . ')</span>' : ''
                ];
            }
        }
        return $rows;
    }

    /**
     * {@inheritdoc}
     */
    public function getRows(): array
    {
        $rows = array_merge(
            $this->getThemes(FRONTEND, Gm::$app->createFrontendTheme(), Gm::t(BACKEND, FRONTEND_NAME)),
            $this->getThemes(BACKEND, Gm::$app->createBackendTheme(), Gm::t(BACKEND, BACKEND_NAME))
        );
        return [
            'total' => sizeof($rows),
            'rows'  => $rows
        ];
    }
}
