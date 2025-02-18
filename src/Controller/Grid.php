<?php
/**
 * Этот файл является частью расширения модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Marketplace\Themes\Controller;

use Gm;
use Gm\Panel\Http\Response;
use Gm\Panel\Helper\ExtGrid;
use Gm\Panel\Helper\HtmlGrid;
use Gm\Filesystem\Filesystem;
use Gm\Mvc\Module\BaseModule;
use Gm\Panel\Widget\TabGrid;
use Gm\Panel\Controller\GridController;
use Gm\Panel\Helper\HtmlNavigator as HtmlNav;

/**
 * Контроллер вывода сетки тем оформлений.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Marketplace\Themes\Controller
 * @since 1.0
 */
class Grid extends GridController
{
    /**
     * {@inheritdoc}
     * 
     * @var BaseModule|\Gm\Backend\Marketplace\Themes\Extension
     */
    public BaseModule $module;

    /**
     * {@inheritdoc}
     */
    public function createWidget(): TabGrid
    {
        /** @var TabGrid $tab Сетка данных (Gm.view.grid.Grid GmJS) */
        $tab = parent::createWidget();

        $tab->grid->router = [
                'rules' => [
                    'delete'     => '{route}/delete',
                    'data'       => '{route}/data',
                    'updateRow'  => '{route}/update?theme={id}',
                    'deleteRow'  => '{route}/delete?theme={id}',
                    'expandRow'  => '{route}/expand?theme={id}'
                ],
                'route' => Gm::alias('@route', '/grid')
            ];

        // столбцы (Gm.view.grid.Grid.columns GmJS)
        $tab->grid->columns = [
            ExtGrid::columnNumberer(),
            ExtGrid::columnAction(),
            [
                'text'      => '#Название',
                'xtype'     => 'templatecolumn',
                'dataIndex' => 'name',
                'tpl'       => HtmlGrid::tag(
                    'div',
                    [
                        HtmlGrid::tplIf('thumb',
                            HtmlGrid::tag('div', '', ['class' => 'gm-mp-themes-grid__cell-icon', 'style' => 'background-image:url({thumb})']),
                            HtmlGrid::tag('div', '', ['class' => 'gm-mp-themes-grid__cell-icon', 'style' => 'background-image:url(' . $tab->imageSrc('/icon-none.svg')  .')'])
                        ),
                        HtmlGrid::tag('div', '{name}', ['class' => 'gm-mp-themes-grid__cell-title']),
                        HtmlGrid::tag('div', '{description}', ['class' => 'gm-mp-themes-grid__cell-desc']),
                        HtmlGrid::tag('div', $this->module->t('Version') . ': <span>{version}</span>', ['class' => 'gm-mp-themes-grid__cell-ver']),
                    ],
                    ['class' => 'gm-mp-themes-grid__cell {clsCellLock}']
                ),
                'cellTip'   => '{name}',
                'filter'    => ['type' => 'string'],
                'width'     => 450
            ],
            [
                'text'      => '#Author',
                'dataIndex' => 'author',
                'cellTip'   => '{author}',
                'filter'    => ['type' => 'string'],
                'tdCls'     => 'gm-mp-themes-grid__td_offset',
                'width'     => 150,
                'hidden'    => true
            ],
            [
                'text'      => '#Version',
                'dataIndex' => 'version',
                'cellTip'   => '{version}',
                'filter'    => ['type' => 'string'],
                'tdCls'     => 'gm-mp-themes-grid__td_offset',
                'width'     => 90
            ],
            [
                'text'      => '#Side',
                'dataIndex' => 'sideName',
                'cellTip'   => '{sideName}',
                'filter'    => ['type' => 'string'],
                'tdCls'     => 'gm-mp-themes-grid__td_offset',
                'width'     => 140
            ],
            [
                'text'      => '#License',
                'dataIndex' => 'license',
                'cellTip'   => '{license}',
                'filter'    => ['type' => 'string'],
                'tdCls'     => 'gm-mp-themes-grid__td_offset',
                'width'     => 140,
                'hidden'    => true
            ],
            [
                'text'      => '#By default',
                'xtype'     => 'g-gridcolumn-switch',
                'tooltip'   => $this->module->t(
                    'You can assign (by default) one of the themes for: {0}, {1}', 
                    [
                        Gm::t(BACKEND, BACKEND_NAME), 
                        Gm::t(BACKEND, FRONTEND_NAME)
                    ]
                ),
                'dataIndex' => 'default',
                'filter'    => ['type' => 'boolean'],
                'width'     => 130
            ],
            [
                'xtype'   => 'g-gridcolumn-control',
                'width'   => 50,
                'tdCls'   => 'gm-mp-themes-grid__td_offset',
                'tooltip' => '#Links to theme templates',
                'items'   => [
                    [
                        'iconCls'   => 'g-icon-svg g-icon_size_20 gm-mp-themes__icon-folders g-icon-m_color_default g-icon-m_is-hover',
                        'dataIndex' => 'templateRoute',
                        'tooltip'   => '#Go to templates theme',
                        'handler'   => 'loadWidgetFromCell'
                    ],
                ]
            ],
            [
                'xtype'    => 'templatecolumn',
                'text'     => '#Status',
                'width'    => 120,
                'align'    => 'center',
                'tdCls'    => 'gm-mp-themes-grid__td_offset',
                'tpl'      => HtmlGrid::tplSwitch(
                    [
                        [
                            HtmlGrid::tag('span', $this->t('not installed'), ['class' => 'gm-mp-themes-status gm-mp-themes-status_not-installed']),
                            '0'
                        ],
                        [
                            HtmlGrid::tag('span', $this->t('installed'), ['class' => 'gm-mp-themes-status gm-mp-themes-status_installed']),
                            '1'
                        ]
                    ],
                    'status'
                ),
                'dataIndex' => 'status'
            ]
        ];

        // панель инструментов (Gm.view.grid.Grid.tbar GmJS)
        $tab->grid->tbar = [
            'padding' => 1,
            'items'   => ExtGrid::buttonGroups([
                'edit' => [
                    'items' => [
                        // инструмент "Создать" (Create)
                        ExtGrid::button([
                            'xtype'         => 'gm-mp-themes-button-create',
                            'iconCls'     => 'g-icon-svg gm-mp-themes__icon-create',
                            'text'          => '#Create',
                            'tooltip'       => '#Creating a theme based on the selected',
                            'msgMustSelect' => '#You need to choose a theme',
                            'caching'       => true,
                        ]),
                        // инструмент "Загрузить" (Upload)
                        ExtGrid::button([
                            'text'        => '#Upload',
                            'tooltip'     => '#Upload a theme',
                            'iconCls'     => 'g-icon-svg gm-mp-themes__icon-upload',
                            'handler'     => 'loadWidget',
                            'handlerArgs' => ['route' => $this->module->route('/upload')]
                        ]),
                        // инструмент "Установить" (Install)+
                        ExtGrid::button([
                            'xtype'       => 'gm-mp-themes-button-install',
                            'iconCls'     => 'g-icon-svg gm-mp-themes__icon-install',
                            'text'        => '#Install',
                            'tooltip'     => '#Install a theme'
                        ]),
                        // инструмент "Удалить" (Uninstall)
                        ExtGrid::button([
                            'xtype'         => 'gm-mp-themes-button-uninstall',
                            'iconCls'       => 'g-icon-svg gm-mp-themes__icon-uninstall',
                            'text'          => '#Uninstall',
                            'tooltip'       => '#Completely delete an installed theme',
                            'msgConfirm'    => '#Are you sure you want to completely delete the installed theme?',
                            'msgMustSelect' => '#You need to select a theme'
                        ]),
                        '-',
                        // инструмент "Удалить" (Delete)+
                        ExtGrid::button([
                            'xtype'         => 'gm-mp-themes-button-delete',
                            'iconCls'       => 'g-icon-svg gm-mp-themes__icon-delete',
                            'text'          => '#Delete',
                            'tooltip'       => '#Delete an uninstalled theme from the repository',
                            'msgConfirm'    => '#Are you sure you want to delete the uninstalled theme from the repository?',
                            'msgMustSelect' => '#You need to select a theme'
                        ]),
                        // инструмент "Демонтаж" (Unmount)+
                        ExtGrid::button([
                            'xtype'         => 'gm-mp-themes-button-unmount',
                            'iconCls'       => 'g-icon-svg gm-mp-themes__icon-unmount',
                            'text'          => '#Unmount',
                            'tooltip'       => '#Delete an installed theme without removing it from the repository',
                            'msgConfirm'    => '#Are you sure you want to remove the installed theme without removing it from the repository?',
                            'msgMustSelect' => '#You need to select a theme'
                        ]),
                        'separator',
                        'edit',
                        'select',
                        'separator',
                        'refresh'
                    ]
                ],
                'columns',
                 // группа инструментов "Поиск"
                 'search' => [
                    'items' => [
                        'help',
                        'search',
                        // инструмент "Фильтр"
                        'filter' => [
                            'form' => [
                                'cls'      => 'g-popupform-filter',
                                'width'    => 400,
                                'height'   => 'auto',
                                'action'   => $this->module->route('/grid/filter', true),
                                'defaults' => ['labelWidth' => 100],
                                'items'    => [
                                    [
                                        'xtype'      => 'radio',
                                        'boxLabel'   => '#All',
                                        'name'       => 'side',
                                        'inputValue' => 'all',
                                    ],
                                    [
                                        'xtype'      => 'radio',
                                        'boxLabel'   => Gm::t(BACKEND, BACKEND_NAME),
                                        'name'       => 'side',
                                        'inputValue' => BACKEND,
                                        'checked'    => true
                                    ],
                                    [
                                        'xtype'      => 'radio',
                                        'boxLabel'   => Gm::t(BACKEND, FRONTEND_NAME),
                                        'name'       => 'side',
                                        'inputValue' => FRONTEND,
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ], [
                'route' => $this->module->route()
            ])
        ];

        // контекстное меню записи (Gm.view.grid.Grid.popupMenu GmJS)
        $tab->grid->popupMenu = [
            'items' => [
                [
                    'text'    => '#Theme information',
                    'iconCls' => 'g-icon-m_info-circle g-icon-m_color_default',
                    'handlerArgs' => [
                        'route'   => $this->module->route('/package/view?id={id}'),
                        'pattern' => 'grid.popupMenu.activeRecord'
                    ],
                    'handler' => 'loadWidget'
                ]
            ]
        ];

        // 2-й клик по строке сетки
        $tab->grid->rowDblClickConfig = [
            'allow' => true,
            'route' => $this->module->route('/form/view/{id}')
        ];

        // количество строк в сетке
        $tab->grid->store->pageSize = 100;
        // локальная фильтрация и сортировка
        $tab->grid->store->remoteFilter = false;
        $tab->grid->store->remoteSort = false;
        // сортировка сетке
        $tab->grid->sorters = [['property' => 'name', 'direction' => 'ASC']];
        // поле аудита записи
        $tab->grid->logField = 'name';
        // плагины сетки
        $tab->grid->plugins = 'gridfilters';
        // класс CSS применяемый к элементу body сетки
        $tab->grid->bodyCls = 'g-grid_background';
        // убрать плагины пагинации сетки
        $tab->grid->pagingtoolbar['plugins'] = [];
        // выбирать только одну запись
        $tab->grid->selModel = ['mode' => 'SINGLE'];

        // панель навигации (Gm.view.navigator.Info GmJS)
        $tab->navigator->info['tpl'] = HtmlNav::tags([
            HtmlNav::tplIf('screenshot',
                HtmlNav::image('{screenshot}', ['width' => '100%'], false),
                HtmlNav::image($tab->imageSrc('/icon-none.svg'), ['width' => '128px'], false)
            ),
            HtmlNav::header('{name}'),
            ['div', '{desc}', ['style' => 'text-align:center;margin-bottom:10px']],
            HtmlNav::fieldLabel($this->t('Name'), '{name}'),
            HtmlNav::fieldLabel($this->t('Author'), '{author}'),
            HtmlNav::fieldLabel($this->t('Version'), '{version}'),
            HtmlNav::fieldLabel($this->t('Side'), '{sideName}'),
            HtmlNav::fieldLabel($this->t('License'), '{license}'),
        ]);

        $tab
            ->setNamespaceJS('Gm.be.mp.themes')
            ->addRequire('Gm.be.mp.themes.Button')
            ->addRequire('Gm.view.grid.column.Switch')
            ->addCss('/grid.css');
        return $tab;
    }

   /**
     * Действие "view" выводит список тем.
     * 
     * @return Response
     */
    public function viewAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();

        /** @var \Gm\Panel\Widget\TabGrid $widget */
        $widget = $this->getWidget();
        // если была ошибка при формировании представления
        if ($widget === false) {
            return $response;
        }

        /** @var \Gm\Panel\Data\Model\GridModel $model модель данных*/
        $model = $this->getModel($this->defaultModel);
        if ($model === false) {
            $response
                ->meta->error(Gm::t('app', 'Could not defined data model "{0}"', [$this->defaultModel]));
            return $response;
        }

        // сброс "dropdown" фильтра таблицы
        $store = $this->module->getStorage();
        $store->directFilter = null; 

        $response
            ->setContent($widget->run())
            ->meta
                ->addWidget($widget);
        return $response;
    }

    /**
     * Действие "delete" удаляет не установленные темы из репозитория.
     *
     * @return Response
     */
    public function deleteAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();
        /** @var string|null $themeId Идентификатор темы */
        $themeId = Gm::$app->request->getPost('id');

        // если тема не выбрана
        if ($themeId === null) {
            $response
                ->meta->error(Gm::t('app', 'Parameter "{0}" not specified', ['id']));
            return $response;
        }

        $chunks = explode('::', $themeId);
        if (sizeof($chunks) < 2) {
            $response
                ->meta->error(Gm::t('app', 'Parameter "{0}" not specified', ['id']));
            return $response;
        }
        /** @var array $themeId Идентификатор темы */
        $themeId = [
            'side' => $chunks[0],
            'name' => $chunks[1]
        ];

        /** @var \Gm\Theme\Theme $theme */
        $theme = Gm::$app->createThemeBySide($themeId['side']);
        if ($theme === null) {
            $response
                ->meta->error(Gm::t('app', 'Parameter "{0}" not specified', ['theme']));
            return $response;
        }
    
        // если тема установлена
        if ($theme->exists($themeId['name'])) {
            $response
                ->meta->error(
                    $this->module->t(
                        'It is not possible to delete the theme "{0}", because it is installed (first dismantle it)', 
                        [$themeId['name']]
                    )
                );
            return $response;
        }

        /** @var array $found Поиск выбранной темы */
        $found = $theme->find(['name' => $themeId['name']]);
        // если тема не найдена
        if (sizeof($found) === 0) {
            $response
                ->meta->error($this->module->t('The theme you have chosen "{0}" does not exist', [$themeId['name']]));
            return $response;
        }

        // локальный путь к теме: '/theme-dir'
        $localPath = $found[0]['localPath'] ?? '';
        if (empty($localPath)) {
            $response
                ->meta->error($this->module->t('The path to the theme is incorrectly specified'));
            return $response;
        }

        // абсолютный путь к теме
        $themePath = $theme->themesPath . $localPath;
        if (!file_exists($themePath)) {
            $response
                ->meta->error($this->module->t('The theme directory "{0}" does not exist', [$themePath]));
            return $response;
        }

        // удаление каталога темы
        if (Filesystem::deleteDirectory($themePath)) {
            $response
                ->meta
                // всплывающие сообщение
                ->cmdPopupMsg(
                    $this->module->t('The theme "{0}" has been successfully deleted', [$themeId['name']]), 
                    $this->module->t('Deleting the theme'), 
                    'success'
                )
                // обновление списка
                ->command('reloadGrid', $this->module->viewId('grid'));
            } else
                $response
                    ->meta->error($this->module->t('Error deleting the "{0}" theme directory', [$themePath])); 
        return $response;
    }
}
