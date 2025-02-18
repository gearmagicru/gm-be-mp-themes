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
use ZipArchive;
use Gm\Panel\Data\Model\FormModel;
use Gm\Filesystem\Filesystem as Fs;
use Gm\Exception\RuntimeException;

/**
 * Модель загрузки темы.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Marketplace\Themes\Model
 * @since 1.0
 */
class UploadForm extends FormModel
{
    /**
     * @var string Событие, возникшее после загрузки файла темы.
     */
    public const EVENT_AFTER_UPLOAD = 'afterUpload';

    /**
     * Имя файла архива темы (включая путь) во временном каталоге.
     * 
     * @see UploadForm::uploadValidate()
     * 
     * @var string
     */
    protected string $filename;

    /**
     * Полный путь к каталогу новой темы.
     * 
     * @see UploadForm::themeValidate()
     * 
     * @var string
     */
    protected string $themePath;

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        if (!class_exists('ZipArchive', false)) {
            throw new RuntimeException('Class "ZipArchive" was not found because PHP "Zip" extension is missing.');
        }

        parent::init();

        $this
            ->on(self::EVENT_AFTER_UPLOAD, function ($result, $message) {
                // всплывающие сообщение
                $this->response()
                    ->meta
                        ->cmdPopupMsg($message['message'], $message['title'], $message['type']);
                /** @var \Gm\Panel\Controller\FormController $controller */
                $controller = $this->controller();
                // обновить список
                $controller->cmdReloadGrid();
            });
    }

    /**
     * {@inheritdoc}
     */
    public function maskedAttributes(): array
    {
        return [
            'side' => 'side',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'side' => $this->module->t('Side'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function validationRules(): array
    {
        return [
            [['side'], 'notEmpty']
        ];
    }

   /**
     * Выполняет проверку загрузки файла.
     * 
     * @return bool
     */
    protected function uploadValidate(): bool
    {
        /** @var \Gm\Uploader\Uploader $uploader */
        $uploader = Gm::$app->uploader;
        $uploader->path = Gm::alias('@runtime');

        // изменение конфигурации загрузчика
        $uploader->transliterateFilename = false;
        $uploader->uniqueFilename = false;
        $uploader->escapeFilename = false;
        $uploader->lowercaseFilename = false;
        $uploader->checkMimeType = true;
        $uploader->checkFileExtension = true;
        $uploader->allowedExtensions = ['zip'];

        /** @var null|\Gm\Uploader\UploadedFile $file */
        $file = $uploader->getFile('themeFile');
        if ($file !== null) {
            if ($file->hasUpload()) {
                // если загружена тема
                if ($file->upload()) {
                    $this->filename = Gm::alias('@runtime') . DS . $file->name;
                } else {
                    $this->addError(Gm::t('app', $file->getErrorMessage()));
                    return false;
                }
            }
        }
        return true;
    }

   /**
     * Выполняет проверку темы.
     * 
     * @return bool
     */
    protected function themeValidate(): bool
    {
        /** @var null|\Gm\Theme\Theme $theme */
        $theme = Gm::$app->createThemeBySide($this->side);
        if ($theme === null) {
            $this->setError(Gm::t('app', 'Parameter "{0}" not specified', ['side']));
            return false;
        }

        // абсолютный путь к загруженной теме
        $this->themePath = $theme->themesPath . DS . Fs::name($this->filename);
        if (file_exists($this->themePath)) {
            $this->setError($this->module->t('The theme directory "{0}" already exists', [$this->themePath]));
            return false;
        }

        // создание каталога новой темы
        if (!Fs::makeDirectory($this->themePath)) {
            $this->setError($this->module->t('Unable to create theme directory "{0}"', [$this->themePath]));
            return false;
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function afterValidate(bool $isValid): bool
    {
        if ($isValid) {
            // проверка назначения
            if ($this->side !== BACKEND && $this->side !== FRONTEND) {
                $this->setError(Gm::t('app', 'Parameter "{0}" not specified', ['side']));
                return false;
            }
            // прорека загрузки файла
            if (!$this->uploadValidate()) {
                return false;
            }
            // проверка темы
            if (!$this->themeValidate()) {
                return false;
            }
        }
        return $isValid;
    }

    /**
     * Выполняет загрузку файла.
     * 
     * @param bool $useValidation Использовать проверку атрибутов (по умолчанию `false`).
     * @param array $attributes Имена атрибутов с их значениями, если не указаны - будут 
     * задействованы атрибуты записи (по умолчанию `null`).
     * 
     * @return bool Возвращает значение `false`, если ошибка загрузки файла темы.
     */
    public function upload(bool $useValidation = false, array $attributes = null): bool
    {
        if ($useValidation && !$this->validate($attributes)) {
            return false;
        }
        return $this->uploadProcess($attributes);
    }

    /**
     * Процесс подготовки загрузки файла темы.
     * 
     * @param null|array $attributes Имена атрибутов с их значениями (по умолчанию `null`).
     * 
     * @return bool Возвращает значение `false`, если ошибка загрузки файла темы.
     */
    protected function uploadProcess(array $attributes = null): bool
    {
        $this->result = false;

        $archive = new ZipArchive();
        $isOpened = $archive->open($this->filename);
        if ($isOpened === true) {
            $this->result = $archive->extractTo($this->themePath);
            $archive->close();
        }

        // удаляем файл архива темы
        Fs::delete($this->filename);

        $this->afterUpload($this->result);
        return $this->result;
    }

    /**
     * Cобытие вызывается после загрузки файла темы.
     * 
     * @see UploadForm::upload()
     * 
     * @param bool $result Если значение `true`, файл темы успешно загружен и распакован.
     * 
     * @return void
     */
    public function afterUpload(bool $result = false): void
    {
        /** @var bool|int $result */
        $this->trigger(
            self::EVENT_AFTER_UPLOAD,
            [
                'result'  => $result,
                'message' => $this->lastEventMessage = $this->uploadMessage($result)
            ]
        );
    }

    /**
     * Возвращает сообщение полученное при загрузке файла темы.
     *
     * @param bool $result Если значение `true`, файл темы успешно загружен и распакован.
     * 
     * @return array Сообщение имеет вид:
     *     [
     *         'success' => true,
     *         'message' => 'Theme file uploaded successfully',
     *         'title'   => 'Uploading a theme',
     *         'type'    => 'accept'
     *     ]
     */
    public function uploadMessage(bool $result): array
    {
        $messages = $this->getActionMessages();
        return [
            'success'  => $result, // успех загрузки
            'message'  => $messages[$result ? 'msgSuccessUpload' : 'msgUnsuccessUpload'], // сообщение
            'title'    => $messages['titleUpload'], // заголовок сообщения
            'type'     => $result ? 'accept' : 'error' // тип сообщения
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getActionMessages(): array
    {
        return [
            'titleUpload'        => $this->module->t('Uploading a theme'),
            'msgUnsuccessUpload' => $this->module->t('Unable to unzip file "{0}"', [$this->filename]),
            'msgSuccessUpload'   => $this->module->t('Theme file uploaded successfully')
        ];
    }
}
