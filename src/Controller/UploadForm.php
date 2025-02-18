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
use Gm\Panel\Widget\Form;
use Gm\Panel\Http\Response;
use Gm\Panel\Helper\ExtForm;
use Gm\Panel\Helper\ExtCombo;
use Gm\Mvc\Module\BaseModule;
use Gm\Panel\Widget\EditWindow;
use Gm\Panel\Controller\FormController;

/**
 * Контроллер загрузки темы.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Marketplace\Themes\Controller
 * @since 1.0
 */
class UploadForm extends FormController
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
    protected string $defaultModel = 'UploadForm';

    /**
     * {@inheritdoc}
     */
    public function createWidget(): EditWindow
    {
        /** @var EditWindow $window */
        $window = parent::createWidget();

        // окно компонента (Ext.window.Window Sencha ExtJS)
        $window->title = '#{upload.title}';
        $window->titleTpl = '#{upload.titleTpl}';
        $window->width = 470;
        $window->padding = 0;
        $window->autoHeight = true;
        $window->layout = 'fit';
        $window->resizable = false;
        $window->iconCls = 'g-icon-m_upload';
        $window->responsiveConfig = [
            'height < 470' => ['height' => '99%'],
            'width < 470' => ['width' => '99%'],
        ];

        // панель формы (Gm.view.form.Panel GmJS)
        $window->form->autoScroll = true;
        $window->form->router->setAll([
            'route' => $this->module->route('/upload'),
            'state' => Form::STATE_CUSTOM,
            'rules' => [
                'submit' => '{route}/complete'
            ] 
        ]);
        $window->form->setStateButtons(
            Form::STATE_CUSTOM,
            ExtForm::buttons([
                'help' => ['subject' => 'upload'], 
                'submit' => ['text' => '#Upload', 'iconCls' => 'g-icon-svg g-icon_size_14 g-icon-m_upload'],
                'cancel'
            ])
        );
        //$window->form->bodyPadding = 7;
        $window->form->items = [
            [
                'xtype'    => 'container',
                'padding'  => 10,
                'defaults' => [
                    'labelAlign' => 'right',
                    'labelWidth' => 120,
                    'width'      => '100%',
                    'allowBlank' => false
                ],
                'items' => [
                    // т.к. параметры ("_csrf", "X-Gjax") не передаются через заголовок, 
                    // то передаём их через метод POST
                    [
                        'xtype' => 'hidden',
                        'name'  => 'X-Gjax',
                        'value' => true
                    ],
                    [
                        'xtype' => 'hidden',
                        'name'  => Gm::$app->request->csrfParamName,
                        'value' => Gm::$app->request->getCsrfTokenFromHeader()
                    ],
                    ExtCombo::side('#Side', 'side', false, ['allowBlank' => false]),
                    [
                        'xtype'      => 'filefield',
                        'name'       => 'themeFile',
                        'fieldLabel' => '#Theme file'
                    ]
                ]
            ],
            [
                'xtype' => 'label',
                'ui'    => 'note',
                'html'  => '#In order to download the theme archive file, you need to select a file with the extension ".zip"'
            ],
        ];
        return $window;
    }

    /**
     * Действие "complete" завершает загрузку файла.
     * 
     * @return Response
     */
    public function completeAction(): Response
    {
        /** @var \Gm\Panel\Http\Response $response */
        $response = $this->getResponse();
        /** @var \Gm\Http\Request $request */
        $request  = Gm::$app->request;

        /** @var \Gm\Backend\Marketplace\Themes\Model\UploadForm $form */
        $form = $this->getModel($this->defaultModel);
        if ($form === false) {
            $response
                ->meta->error(Gm::t('app', 'Could not defined data model "{0}"', [$this->defaultModel]));
            return $response;
        }

        if ($this->useAppEvents) {
            Gm::$app->doEvent($this->makeAppEventName(), [$this, $form]);
        }

        // загрузка атрибутов в модель из запроса
        if (!$form->load($request->getPost())) {
            $response
                ->meta->error(Gm::t(BACKEND, 'No data to perform action'));
            return $response;
        }

        // валидация атрибутов модели
        if (!$form->validate()) {
            $response
                ->meta->error(Gm::t(BACKEND, 'Error filling out form fields: {0}', [$form->getError()]));
            return $response;
        }

        // загрузка файла темы
        if (!$form->upload()) {
            $response
                ->meta->error(
                    $form->hasErrors() ? $form->getError() : $this->module->t('Theme uploading error')
                );
            return $response;
        }

        if ($this->useAppEvents) {
            Gm::$app->doEvent($this->makeAppEventName('After'), [$this, $form]);
        }
        return $response;
    }
}
