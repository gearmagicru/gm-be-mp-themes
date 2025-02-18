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
use Gm\Mvc\Module\BaseModule;
use Gm\Panel\Controller\FormController;
use Gm\Backend\Marketplace\Themes\Widget\CreateWindow;

/**
 * Контроллер формы создания новой темы.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Marketplace\Themes\Controller
 * @since 1.0
 */
class CreateForm extends FormController
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
    protected string $defaultModel = 'CreateForm';

    /**
     * {@inheritdoc}
     * 
     * @return CreateWindow
     */
    public function createWidget(): CreateWindow
    {
        return new CreateWindow();
    }

    /**
     * Действие "view" выводит интерфейс окна создания новой темы.
     * 
     * @return Response
     */
    public function viewAction(): Response
    {
        /** @var \Gm\Panel\Http\Response $response */
        $response = $this->getResponse();

        /** @var \Gm\Backend\Marketplace\Themes\Model\CreateForm $model */
        $model = $this->getModel($this->defaultModel);
        if ($model === false) {
            $response
                ->meta->error(Gm::t('app', 'Could not defined data model "{0}"', [$this->defaultModel]));
            return $response;
        }

        if ($this->useAppEvents) {
            Gm::$app->doEvent($this->makeAppEventName(), [$this, $model]);
        }

        // валидация темы перед выводом формы
        if (!$model->validateBeforeView()) {
            $response
                ->meta->error($model->getError());
            return $response;
        }

        /** @var null|array $info Информация о выбранной теме */
        $info = $model->getThemeInfo();

        /** @var \Gm\Panel\Widget\EditWindow $widget */
        $widget = $this->getWidget();
        $widget->info = [
            'id'        => Gm::$app->request->getPost('id'),
            'name'      => 'New ' . $info['name'],
            'localPath' => '/new-' . mb_strtolower(str_replace(' ', '-', $info['name']))
        ];

        $response
            ->setContent($widget->run())
            ->meta
                ->addWidget($widget);

        if ($this->useAppEvents) {
            Gm::$app->doEvent($this->makeAppEventName('After'), [$this, $model]);
        }
        return $response;
    }

    /**
     * Действие "complete" завершает создание темы.
     * 
     * @return Response
     */
    public function completeAction(): Response
    {
        /** @var \Gm\Panel\Http\Response $response */
        $response = $this->getResponse();
        /** @var \Gm\Http\Request $request */
        $request = Gm::$app->request;

        /** @var \Gm\Backend\Marketplace\Themes\Model\CreateForm $model */
        $model = $this->getModel($this->defaultModel);
        if ($model === false) {
            $response
                ->meta->error(Gm::t('app', 'Could not defined data model "{0}"', [$this->defaultModel]));
            return $response;
        }

        if ($this->useAppEvents) {
            Gm::$app->doEvent($this->makeAppEventName(), [$this, $model]);
        }

        // загрузка атрибутов в модель из запроса
        if (!$model->load($request->getPost())) {
            $response
                ->meta->error(Gm::t(BACKEND, 'No data to perform action'));
            return $response;
        }

        // валидация атрибутов темы
        if (!$model->validate()) {
            $response
                ->meta->error(Gm::t(BACKEND, 'Error filling out form fields: {0}', [$model->getError()]));
            return $response;
        }

        // валидация выбранной темы
        if (!$model->validateTheme()) {
            $response
                ->meta->error($model->getError());
            return $response;
        }

        // создание темы
        if (!$model->create()) {
            $response
                ->meta->error(
                    $model->hasErrors() ? $model->getError() : $this->module->t('Unable to create theme')
                );
            return $response;
        }

        if ($this->useAppEvents) {
            Gm::$app->doEvent($this->makeAppEventName('After'), [$this, $model]);
        }
        return $response;
    }
}
