<?php
/**
 * Этот файл является частью расширения модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru/framework/
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Marketplace\Themes\Widget;

use Gm\Helper\Str;
use Gm\Panel\Widget\Form;
use Gm\Panel\Widget\Window;

/**
 * Виджет формирования интерфейса окна установки темы.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Marketplace\Themes\Widget
 * @since 1.0
 */
class InstallWindow extends Window
{
    /**
     * Виджет для формирования интерфейса формы.
     * 
     * @var Form
     */
    public Form $form;

    /**
     * Информация о теме.
     * 
     * Обязательно должен содержать ключ "id", где значение может принимать:
     * 'backend::ThemeName', 'frontend::ThemeName'.
     * 
     * @var array
     */
    public array $info = [];

    /**
     * {@inheritdoc}
     */
    public array $requires = [
        'Gm.view.window.Window',
        'Gm.view.form.Panel'
    ];

    /**
     * {@inheritdoc}
     */
    protected function init(): void
    {
        parent::init();

        // панель формы (Gm.view.form.Panel GmJS)
        $this->form = new Form([
            'id'     => 'iform', // => gm-mp-themes-iform
            'router' => [
                'route' => $this->creator->route('/install'),
                'state' => Form::STATE_CUSTOM,
                'rules' => [
                    'complete' => '{route}/complete'
                ] 
            ],
            'bodyPadding' => 7,
            'defaults'    => [
                'labelWidth' => 120,
                'labelAlign' => 'right',
            ],
            'items' => []
        ], $this);

        // окно (Ext.window.Window Sencha ExtJS)
        $this->id         = 'gm-mp-themes-iwindow';
        $this->iconCls    = 'gm-mp-themes__icon-install';
        $this->cls        = 'g-window_install';
        $this->width      = 450;
        $this->autoHeight = true;
        $this->layout     = 'fit';
        $this->resizable  = false;
        $this->ui         = 'install';
        $this->items      = [$this->form];
        $this->responsiveConfig = [
            'height < 450' => ['height' => '99%'],
            'width < 450' => ['width' => '99%'],
        ];
    }

    /**
     * Возвращает заголовок окна.
     * 
     * @return string
     */
    protected function renderTitle(): string
    {
        if ($this->title) return $this->title;

        return sprintf('%s <span>%s</span>',
            $this->creator->t('{install.title}', [$this->info['name'] ?? SYMBOL_NONAME]),
            Str::ellipsis($this->info['description'] ?? '', 0, 60)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function beforeRender(): bool
    {
        $this->title = $this->renderTitle();

        $keywords = $this->info['keywords'] ?? '';
        if (is_array($keywords)) {
            $keywords = implode(', ', $keywords);
        }

        $this->form->items = [
            [
                'xtype' => 'hidden',
                'name'  => 'id',
                'value' => $this->info['id'] ?? ''
            ],
            [
                'xtype'      => 'displayfield',
                'ui'         => 'parameter',
                'fieldLabel' => '#Name',
                'value'      => $this->info['name'] ?? ''
            ],
            [
                'xtype'      => 'displayfield',
                'ui'         => 'parameter',
                'fieldLabel' => '#Description',
                'value'      => $this->info['description'] ?? ''
            ],
            [
                'xtype'      => 'displayfield',
                'ui'         => 'parameter',
                'fieldLabel' => '#Author',
                'value'      => $this->info['author'] ?? ''
            ],
            [
                'xtype'      => 'displayfield',
                'ui'         => 'parameter',
                'fieldLabel' => '#License',
                'value'      => $this->info['license'] ?? ''
            ],
            [
                'xtype'      => 'displayfield',
                'ui'         => 'parameter',
                'fieldLabel' => '#Version',
                'value'      => $this->info['version'] ?? ''
            ],
            [
                'xtype'      => 'displayfield',
                'ui'         => 'parameter',
                'fieldLabel' => '#Keywords',
                'value'      => $keywords
            ]
        ];

        // панель кнопок формы (Ext.form.Panel.buttons Sencha ExtJS)
        $this->form->setStateButtons(
            Form::STATE_CUSTOM,
            [
                'help' => ['subject' => 'install'],
                'action' => [
                    'iconCls'     => 'g-icon-svg g-icon_size_14 g-icon-m_save-1',
                    'text'        => '#Install',
                    'handlerArgs' => ['routeRule' => 'complete'],
            ],
            'cancel'
        ]);
        return true;
    }
}
