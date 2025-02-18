<?php
/**
 * Этот файл является частью расширения модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Marketplace\Themes\Model;

use Gm;
use Gm\Theme\Theme;
use Gm\Panel\Data\Model\ArrayGridModel;

/**
 * Модель данных вывода сетки тем оформлений.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Marketplace\Themes\Model
 * @since 1.0
 */
class Grid extends ArrayGridModel
{
    /**
     * Вид назначения: `BACKEND`, `FRONTEND`.
     * 
     * @var array
     */
    protected array $sideNames = [];

    /**
     * Тема Панели управления.
     * 
     * @var Theme
     */
    protected Theme $beTheme;

    /**
     * Тема сайта.
     * 
     * @var Theme
     */
    protected Theme $feTheme;

    /**
     * Статусы тем.
     * 
     * @var array
     */
    protected array $statuses = [];

    /**
     * {@inheritdoc}
     */
    public function getDataManagerConfig(): array
    {
        return [
            'fields' => [
                ['id'],
                ['screenshot'],
                ['thumb'],
                ['name'],
                ['description'],
                ['author'],
                ['version'],
                ['sideName'],
                ['license'],
                ['default'],
                ['status'],
                ['templateRoute']
            ],
            'filter' => [
                'side' => ['operator' => '='],
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this
            ->on(self::EVENT_AFTER_DELETE, function ($someRecords, $result, $message) {
                // всплывающие сообщение
                $this->response()
                    ->meta
                        ->cmdPopupMsg($message['message'], $message['title'], $message['type']);
                /** @var \Gm\Panel\Controller\GridController $controller */
                $controller = $this->controller();
                // обновить список
                $controller->cmdReloadGrid();
            })
            ->on(self::EVENT_AFTER_SET_FILTER, function ($filter) {
                /** @var \Gm\Panel\Controller\GridController $controller */
                $controller = $this->controller();
                // обновить список
                $controller->cmdReloadGrid();
            });
    }

    /**
     * {@inheritdoc}
     */
    public function beforeFetchRows(): void
    {
        $this->statuses = [
            $this->module->t('not installed'),
            $this->module->t('installed')
        ];
        $this->sideNames = [
            BACKEND  => Gm::t(BACKEND, BACKEND_NAME),
            FRONTEND => Gm::t(BACKEND, FRONTEND_NAME),
        ];
    }

    /**
     * {@inheritdoc}
     * 
     * @return array
     */
    public function buildQuery($builder): array
    {
        $this->beTheme = Gm::$app->createBackendTheme();
        $this->feTheme = Gm::$app->createFrontendTheme();

        /** @var string $side Фильтр / Назначение  */
        $side = $this->directFilter ? ($this->directFilter['side']['value'] ?? '') : 'all';
        switch($side) {
            // все (сайт + панель управления)
            case 'all':
                return array_merge($this->beTheme->find(), $this->feTheme->find());

            // панель управления
            case BACKEND: return $this->beTheme->find();

            // сайт
            case FRONTEND: return $this->feTheme->find();
        }
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function beforeFetchRow(mixed $row, int|string $rowKey): ?array
    {
        // вид назначения: `BACKEND`, `FRONTEND`
        $side = $row['side'] ?? '';
        if ($side === BACKEND)
            $theme = $this->beTheme;
        else
        if ($side === FRONTEND)
            $theme = $this->feTheme;
        else {
            $theme = null;
            $row['status'] = 0;
        }

        // идентификатор темы
        $id = $side . '::' . $row['name'];
        // маршрут ка шаблону
        $templateRoute = null;
        // пункты всплывающего меню записи
        $popupMenuItems = [];
        // тема по умолчанию
        $default = -1;

        // если тема имеет назначение
        if ($theme) {
            // название назначения
            $row['sideName'] = $this->sideNames[$side] ?? $side;
            // статус темы
            $row['status'] = $theme->exists($row['name']) ? 1 : 0;
            // если тема установлена
            if ($row['status']) {
                $default = $theme->default === $row['name'] ? 1 : 0;
                $popupMenuItems = [[0, 'enabled']];
                $templateRoute = '@backend/templates?themeName=' . $id;
            } else {
                $popupMenuItems = [[0, 'disabled']];
                $templateRoute = '::disabled';
            }
        }
        return [
            'id'          => $id,
            'thumb'       => $row['thumb'],
            'screenshot'  => $row['screenshot'],
            'name'        => $row['name'],
            'description' => $row['description'],
            'author'      => $row['author'],
            'version'     => $row['version'],
            'sideName'    => $row['sideName'],
            'license'     => $row['license'],
            'default'     => $default,
            'status'      => $row['status'],
            'templateRoute'  => $templateRoute,
            'popupMenuItems' => $popupMenuItems,
            'popupMenuTitle' => $row['name']
        ];
    }
}
