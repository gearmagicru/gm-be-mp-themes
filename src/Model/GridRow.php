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
use Closure;
use Gm\Db\Sql\Where;
use Gm\Panel\Data\Model\FormModel;
use Gm\Panel\Data\Model\Exception\ColumnException;

/**
 * Модель данных профиля записи темы.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Marketplace\Themes\Model
 * @since 1.0
 */
class GridRow extends FormModel
{
    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this
            ->on(self::EVENT_AFTER_SAVE, function ($isInsert, $columns, $result, $message) {
                if ($message['success']) {
                    $message['title']   = $this->module->t('Setting the default theme');
                    $message['message'] = $this->module->t(
                        'The theme "{0}" is set as the default for "{1}"', 
                        [$this->default,  $this->side ? Gm::t('app', ucfirst($this->side)) : SYMBOL_NONAME]
                    );
                }
                /** @var \Gm\Panel\Controller\GridController $controller */
                $controller = $this->controller();
                // обновить список
                $controller->cmdReloadGrid();
                // всплывающие сообщение
                $this->response()
                    ->meta
                        ->cmdPopupMsg($message['message'], $message['title'], $message['type']);
            });
    }

    /**
     * {@inheritdoc}
     */
    public function maskedAttributes(): array
    {
        return [
            'side'      => 'side', // сторона: `BACKEND`, `FRONTEND`
            'available' => 'available', // доступные темы
            'default'   => 'default' // имя активной темы
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function beforeValidate(array &$attributes): bool
    {
        // если попытка столбцу "По умолчанию" вернуть флаг в false (это не логично, 
        // но и заблокировать не возможно)
        if (isset($attributes['default']) && $attributes['default'] == 0) {
            throw new ColumnException($this->t('In the "By default" column, you can only select the default theme with the switch, but not disable'));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function updateRecord(array $columns, Where|Closure|string|array $where = null): false|int
    {
        /** @var null|array $identifier */
        $identifier = $this->getIdentifier();
        if ($identifier === null) {
            return false;
        }

        /** @var null|\Gm\Theme\Theme */
        $theme = Gm::$app->createThemeBySide($identifier['side']);
        // если тема не определилась
        if ($theme === null) {
            return false;
        }

        // текущая тема
        $this->attributes['default'] = $identifier['name'];
        return Gm::$app->unifiedConfig
            ->set($theme->unifiedName, $this->attributes)
            ->save();
    }

    /**
     * {@inheritdoc}
     * 
     * @return null|array
     */
    public function getIdentifier(): ?array
    {
        /** @var string $theme Тема: {side}::{name} */
        $theme = Gm::$app->request->get('theme', '');
        if ($theme) {
            $chunks = explode('::', $theme);
            if (sizeof($chunks) > 1) {
                return [
                    'side' => $chunks[0],
                    'name' => $chunks[1]
                ];
            }
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function get(mixed $identifier = null): ?static
    {
        if ($identifier === null) {
            $identifier = $this->getIdentifier();
        }

        if ($identifier) {
            /** @var null|\Gm\Theme\Theme */
            $theme = Gm::$app->createThemeBySide($identifier['side']);
            // если тема не определилась
            if ($theme === null) {
                return null;
            }

            /** @var null|array $row Параметры выбранной темы из идентификатора */
            $row = Gm::$app->unifiedConfig->get($theme->unifiedName);
            if ($row === null) {
                $row = [
                    'side'      => $identifier['side'],
                    'default'   => $theme->default,
                    'available' => $theme->available
                ];
            }

            $this->reset();
            $this->afterSelect();
            $this->populate($this, $row);
            $this->afterPopulate();
            return $this;
        }
        return null;
    }
}
