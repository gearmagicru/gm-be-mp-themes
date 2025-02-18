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
use Gm\Panel\Data\Model\FormModel;

/**
 * Модель создание темы на основе выбранной.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Marketplace\Themes\Model
 * @since 1.0
 */
class PackageForm extends FormModel
{
    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this
            ->on(self::EVENT_AFTER_SAVE, function ($isInsert, $columns, $result, $message) {
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
    public function getIdentifier(): mixed
    {
        if ($this->identifier === null) {
            $chunks = explode('::', Gm::$app->request->get('id', ''));
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
    public function get(mixed $identifier = null): ?static
    {
        if ($identifier === null)
            $identifier = $this->getIdentifier();
        else {
            if (is_string($identifier))
                $chunks = explode('::', $identifier);
            else
                $chunks = $identifier;
            $identifier = [
                'side' => $chunks[0] ?? '',
                'name' => $chunks[1] ?? ''
            ];
        }

        if ($identifier) {
            /** @var null|Theme */
            $theme = Gm::$app->createThemeBySide($identifier['side']);
            if ($theme) {
                /** @var null|ThemePackage $package Пакет информации о теме */
                $package = $theme->getPackage($identifier['name']);
                if ($package) {
                    /** @var null|array $attributes */
                    $attributes = $package->getInfo();
                    if ($attributes) {
                        $this->setOldAttributes($attributes);
                        $this->setAttributes($attributes);
                        return $this;
                    }
                }
            }
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function maskedAttributes(): array
    {
        return [
            'name'        => 'name',
            'description' => 'description',
            'author'      => 'author',
            'license'     => 'license',
            'version'     => 'version',
            'keywords'    => 'keywords'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'name'        => $this->module->t('Name'),
            'description' => $this->module->t('Description'),
            'author'      => $this->module->t('Author'),
            'license'     => $this->module->t('License'),
            'version'     => $this->module->t('Version'),
            'keywords'    => $this->module->t('Keywords')
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function validationRules(): array
    {
        return [
            [['name', 'version'], 'notEmpty']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function formatterRules(): array
    {
        return [
            [['name', 'description', 'author', 'license', 'version'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function save(bool $useValidation = false,  array $attributeNames = null): bool|int|string
    {
        return $this->update($useValidation, $attributeNames) !== false;
    }

    /**
     * {@inheritdoc}
     */
    public function getActionTitle(): string
    {
        $id = $this->getIdentifier();
        return $id['name'] ?? '';
    }

    /**
     * {@inheritdoc}
     */
    public function afterValidate(bool $isValid): bool
    {
        if ($isValid) {
            $themeId = $this->getIdentifier();

            if (empty($themeId['side']) || empty($themeId['name'])) {
                $this->setError(Gm::t('app', 'Parameter "{0}" not specified', ['id']));
                return false;
            }

            /** @var null|\Gm\Theme\Theme $theme */
            $theme = Gm::$app->createThemeBySide($themeId['side']);
            if ($theme === null) {
                $this->setError(Gm::t('app', 'Parameter "{0}" not specified', ['id']));
                return false;
            }

            // если тема с именем уже существует
            if ($this->name !== $themeId['name']) {
                if ($theme->exists($this->name)) {
                    $this->setError($this->module->t('A theme with the specified name "{0}" already exists', [$this->name]));
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeUpdate(array &$attributes): void
    {
        // ключевые слова: 'word, word' => ['word', 'word']
        if (!empty($attributes['keywords'])) {
            $keywords = $attributes['keywords'];
            if (is_string($keywords)) {
                $arr = explode(',', $keywords);
                $keywords = [];
                foreach ($arr as $word) {
                    $keywords[] = trim($word);
                }
                $attributes['keywords'] = $keywords;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function updateProcess(array $attributes = null): false|int
    {
        if (!$this->beforeSave(false)) {
            return false;
        }

        // возвращает атрибуты без псевдонимов (если они были указаны)
        $attributes = $this->unmaskedAttributes($this->attributes);
        $this->beforeUpdate($attributes);

        // изменение записи
        $result = $this->updatePackage($attributes);
        $this->result = $result === true ? 1 : $result;

        $this->setOldAttributes($this->attributes);
        $this->afterSave(false, $attributes, $this->result);
        return $this->result;
    }

    /**
     * {@inheritdoc}
     */
    public function updatePackage(array $attributes): bool
    {
        /** @var array $id Идентификатор обновляемой темы */
        $id = $this->getIdentifier();

        if (empty($id['side']) || empty($id['name'])) {
            $this->setError(Gm::t('app', 'Parameter "{0}" not specified', ['id']));
            return false;
        }

        /** @var null|\Gm\Theme\Theme $theme */
        $theme = Gm::$app->createThemeBySide($id['side']);
        if ($theme === null) {
            $this->setError(Gm::t('app', 'Parameter "{0}" not specified', ['id']));
            return false;
        }

        // если тема не найдена
        if (!$theme->exists($id['name'])) {
            $this->setError($this->module->t('The theme you have chosen "{0}" does not exist', [$id['name']]));
            return false;
        }

        /** @var null|ThemePackage $package Пакет информации о теме */
        $package = $theme->getPackage($id['name']);
        if ($package === null) {
            $this->setError($this->module->t('Unable to get information for the theme "{0}"', [$id['name']]));
            return false;
        }
        if ($package->save($attributes)) {
            // если тема была по умолчанию, то меняем имя
            if ($theme->default === $id['name']) {
                $theme->default = $this->name;
            }

            // если тема была установлена, то меняем
            if (isset($theme->available[$id['name']])) {
                $theme->available[$this->name] = $theme->available[$id['name']];
                $theme->available[$this->name]['name'] = $this->name;
                unset($theme->available[$id['name']]);
            }

            // параметры выбранной темы из идентификатора
            $themeParams = [
                'side'      => $id['side'],
                'default'   => $theme->default,
                'available' => $theme->available
            ];

            return  Gm::$app->unifiedConfig
                ->set($theme->unifiedName, $themeParams)
                ->save();
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function processing(): void
    {
       if (is_array($this->keywords)) {
            $this->keywords = implode(', ', $this->keywords);
       }
    }

    /**
     * {@inheritdoc}
     */
    protected function getActionMessages(): array
    {
        return [
            'titleUpdate'        => $this->module->t('Change information'),
            'msgSuccessUpdate'   => $this->module->t('Theme info updated successfully'),
            'msgUnsuccessUpdate' => $this->module->t('Theme info update error')
        ];
    }
}
