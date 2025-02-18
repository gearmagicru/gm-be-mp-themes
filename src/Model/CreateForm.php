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
use Gm\Theme\ThemePackage;
use Gm\Filesystem\Filesystem;
use Gm\Panel\Data\Model\FormModel;

/**
 * Модель создание темы на основе выбранной.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Marketplace\Themes\Model
 * @since 1.0
 */
class CreateForm extends FormModel
{
    /**
     * @var string Событие, возникшее после создания темы.
     */
    public const EVENT_AFTER_CREATE = 'afterCreate';

    /**
     * @var Theme|null
     */
    protected ?Theme $theme = null;

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this
            ->on(self::EVENT_AFTER_CREATE, function ($result, $name, $message) {
                // всплывающие сообщение
                $this->response()
                    ->meta
                        ->cmdPopupMsg($this->module->t($message['message'], [$name]), $message['title'], $message['type']);
                /** @var \Gm\Panel\Controller\FormController $controller */
                $controller = $this->controller();
                // обновить список
                $controller->cmdReloadGrid();
            });
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier(): mixed
    {
        if ($this->identifier === null) {
            $chunks = explode('::', Gm::$app->request->getPost('id', ''));
            $this->identifier = [
                'side' => $chunks[0] ?? '',
                'name' => $chunks[1] ?? ''
            ];
        }
        return $this->identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function maskedAttributes(): array
    {
        return [
            'id'          => 'id',
            'localPath'   => 'localPath',
            'name'        => 'name',
            'description' => 'description',
            'author'      => 'author',
            'license'     => 'license',
            'version'     => 'version'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'localPath'   => $this->module->t('Catalog'),
            'name'        => $this->module->t('Name'),
            'description' => $this->module->t('Description'),
            'author'      => $this->module->t('Author'),
            'license'     => $this->module->t('License'),
            'version'     => $this->module->t('Version'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function validationRules(): array
    {
        return [
            [['id', 'localPath', 'name', 'version'], 'notEmpty']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function formatterRules(): array
    {
        return [
            [['localPath', 'name', 'description', 'author', 'license', 'version'], 'safe']
        ];
    }

    /**
     * @param bool $useValidation
     * 
     * @return bool
     */
    public function create(bool $useValidation = false): bool
    {
        if ($useValidation) {
            if (!$this->validate() || !$this->validateTheme()) {
                return false;
            }
        }
        return $this->createProcess();
    }

    /**
     * @return Theme|null
     */
    public function getTheme(): ?Theme
    {
        if ($this->theme === null) {
            $identifier = $this->getIdentifier();
            $this->theme = Gm::$app->createThemeBySide($identifier['side']);
        }
        return $this->theme;
    }

    /**
     * Возвращает информацию о выбранной теме (на основе которой создаётся новая тема).
     * 
     * @return array|null
     */
    public function getThemeInfo(): ?array
    {
        $theme = $this->getTheme();
        if ($theme) {
            $identifier = $this->getIdentifier();
            return $theme->get($identifier['name']);
        }
        return null;
    }

    /**
     * @return bool
     */
    public function validateBeforeView(): bool
    {
        $themeId = $this->getIdentifier();

        if (empty($themeId['side']) || empty($themeId['name'])) {
            $this->setError(Gm::t('app', 'Parameter "{0}" not specified', ['id']));
            return false;
        }

        /** @var null|\Gm\Theme\Theme $theme */
        $theme = $this->getTheme();
        if ($theme === null) {
            $this->setError(Gm::t('app', 'Parameter "{0}" not specified', ['id']));
            return false;
        }

        // если тема не найдена
        if (!$theme->exists($themeId['name'])) {
            $this->setError($this->module->t('The theme you have chosen "{0}" does not exist', [$themeId['name']]));
            return false;
        }

        /** @var array $themeInfo Информация о теме */
        $themeInfo = $theme->get($themeId['name']);
        if (empty($themeInfo)) {
            $this->setError($this->module->t('Unable to get information for the theme "{0}"', [$themeId['name']]));
            return false;
        }
        return true;
    }

    /**
     * @return bool
     */
    public function validateTheme(): bool
    {
        $themeId = $this->getIdentifier();

        if (empty($themeId['side']) || empty($themeId['name'])) {
            $this->setError(Gm::t('app', 'Parameter "{0}" not specified', ['id']));
            return false;
        }

        /** @var null|\Gm\Theme\Theme $theme */
        $theme = $this->getTheme();
        if ($theme === null) {
            $this->setError(Gm::t('app', 'Parameter "{0}" not specified', ['id']));
            return false;
        }

        // если тема не найдена
        if (!$theme->exists($themeId['name'])) {
            $this->setError($this->module->t('The theme you have chosen "{0}" does not exist', [$themeId['name']]));
            return false;
        }

        // если новая тема с именем уже существует
        if ($theme->exists($this->name)) {
            $this->setError($this->module->t('A theme with the specified name "{0}" already exists', [$this->name]));
            return false;
        }

        /** @var array $themeInfo Информация о теме */
        $themeInfo = $theme->get($themeId['name']);
        if (empty($themeInfo)) {
            $this->setError($this->module->t('Unable to get information for the theme "{0}"', [$themeId['name']]));
            return false;
        }

        // локальный путь к теме: '/theme-dir'
        $localPath = $themeInfo['localPath'] ?? '';
        if (empty($localPath)) {
            $this->setError($this->module->t('The path to the theme is incorrectly specified'));
            return false;
        }

        // абсолютный путь к теме (на основе которой создаётся новая тема)
        $themePath = $theme->themesPath . $localPath;
        if (!file_exists($themePath)) {
            $this->setError($this->module->t('The theme directory "{0}" does not exist', [$themePath]));
            return false;
        }

        // проверка каталога новой темы
        if (file_exists($theme->themesPath . $this->localPath)) {
            $this->setError($this->module->t('A theme with the specified directory "{0}" already exists', [$this->localPath]));
            return false;
        }
        return true;
    }

    /**
     * @return bool
     */
    public function createProcess(): bool
    {
        $themeId = $this->getIdentifier();

        /** @var Theme $theme Тема (на основе которой создаётся новая тема) */
        $theme = $this->getTheme();
        /** @var array $themeInfo Информация о теме (на основе которой создаётся новая тема) */
        $themeInfo = $theme->get($themeId['name']);

        // абсолютный путь к теме (на основе которой создаётся новая тема)
        $oldThemePath = $theme->themesPath . $themeInfo['localPath'];
        // абсолютный путь к новой теме
        $themePath = $theme->themesPath . $this->localPath;
        // создание каталога новой темы
        if (!Filesystem::makeDirectory($themePath)) {
            $this->setError($this->module->t('Unable to create theme directory "{0}"', [$themePath]));
            return false;
        }

        // копирование файлов в новую тему
        if (!Filesystem::copyDirectory($oldThemePath, $themePath)) {
            $this->setError($this->module->t('Unable to copy theme files "{0}" to directory "{1}"', [$themeInfo['name'], $themePath]));
            return false;
        }

        /** @var ThemePackage $package Пакет новой темы   */
        $package = new ThemePackage($themePath);
        // если пакет новой темы не найден
        if (!$package->exists()) {
            $this->setError($this->module->t('File "{0}" theme information package "{1}" not found', [$package->getFilename(), $this->name]));
            return false;
        }

        // изменение пакета информации новой темы
        $properties = [
            'version'     => $this->version,
            'name'        => $this->name,
            'description' => $this->description,
            'author'      => $this->author,
            'license'     => $this->license,
            'keywords'    => [],
        ];
        if (!$package->save($properties)) {
            $this->setError($this->module->t('Unable to save package info for theme "{0}"', [$this->name]));
            return false;
        }

        /** @var null|array $themeParams Параметры выбранной темы из идентификатора */
        $themeParams = Gm::$app->unifiedConfig->get($theme->unifiedName);
        if ($themeParams === null) {
            $themeParams = [
                'side'      => $themeId['side'],
                'default'   => $theme->default,
                'available' => $theme->available
            ];
        }

        // добавляем информацию о новой теме
        $themeParams['available'][$this->name] = [
            'name'      => $this->name,
            'localPath' => $this->localPath
        ];

        /** @var bool $result */
        $result = Gm::$app->unifiedConfig
            ->set($theme->unifiedName, $themeParams)
            ->save();

        $this->afterCreate($result, $this->name);
        return $result;
    }

    /**
     * Событие вызывается после создания темы.
     * 
     * @see CreateForm::createProcess()
     * 
     * @param bool $result Если значение `true`, тема успешо добавлена.
     * @param string $name Название темы.
     * 
     * @return void
     */
    public function afterCreate(bool $result, string $name): void
    {
        /** @var bool|int $result */
        $this->trigger(
            self::EVENT_AFTER_CREATE,
            [
                'result'  => $result,
                'name'    => $name,
                'message' => $this->lastEventMessage = $this->createMessage($result)
            ]
        );
    }

    /**
     * Возвращает сообщение полученное при создании темы.
     * 
     * @see CreateForm::afterCreate()
     *
     * @param bool $result Если значение `true`, тема успешо добавлена.
     * 
     * @return array Сообщение имеет вид:
     *     [
     *         "success" => true,
     *         "message" => "Theme successfully created",
     *         "title"   => "Create a theme",
     *         "type"    => "accept"
     *     ]
     */
    public function createMessage(bool $result): array
    {
        $messages = $this->getActionMessages();
        return [
            'success'  => $result, // успех
            'message'  => $messages[$result ? 'msgSuccessCreate' : 'msgUnsuccessCreate'], // сообщение
            'title'    => $messages['titleCreate'], // заголовок сообщения
            'type'     => $result ? 'accept' : 'error' // тип сообщения
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getActionMessages(): array
    {
        return [
            'titleCreate'        => $this->module->t('Create a theme'),
            'msgSuccessCreate'   => $this->module->t('Theme "{0}" created successfully'),
            'msgUnsuccessCreate' => $this->module->t('Unable to create theme')
        ];
    }
}
