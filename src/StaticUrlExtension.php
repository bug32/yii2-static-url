<?php

namespace bug32\staticUrl;

use yii\base\BootstrapInterface;
use yii\base\Module;
use yii\console\Application as ConsoleApplication;
use yii\web\Application as WebApplication;

/**
 * Static URL Extension for Yii2
 *
 * @package bug32\staticUrl
 * @version 1.0.0
 */
class StaticUrlExtension extends Module implements BootstrapInterface
{
    public bool   $enableAdminInterface  = TRUE;
    public bool   $enableConsoleCommands = TRUE;
    public string $adminRoute            = 'static-url/backend';
    public int    $defaultStatus         = 10;
    public string $urlValidationPattern  = '/^[a-z0-9\-_\/]+$/';

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'bug32\\staticUrl\\controllers';

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();
        \Yii::setAlias('@staticUrl', __DIR__);
        $this->registerTranslations();

        // Применяем параметры из конфигурации
        $config = $this->module && is_array($this->module->params ?? NULL) ? $this->module->params : [];
        foreach ([
                     'enableAdminInterface', 'enableConsoleCommands', 'adminRoute', 'defaultStatus',
                     'urlValidationPattern'] as $param) {
            if (isset($this->$param)) continue;
            if (isset($config[$param])) {
                $this->$param = $config[$param];
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function bootstrap($app): void
    {
        if ($app instanceof WebApplication) {
            $app->setComponents([
                'staticUrlRule' => [
                    'class' => 'bug32\\staticUrl\\components\\StaticUrlRule',
                ],
            ]);
            if (isset($app->urlManager->rules)) {
                $hasStaticRule = FALSE;
                foreach ($app->urlManager->rules as $rule) {
                    if ($rule instanceof \bug32\staticUrl\components\StaticUrlRule) {
                        $hasStaticRule = TRUE;
                        break;
                    }
                }
                if (!$hasStaticRule) {
                    array_unshift($app->urlManager->rules, \Yii::createObject([
                        'class' => 'bug32\\staticUrl\\components\\StaticUrlRule',
                    ]));
                }
            }
        } elseif ($app instanceof ConsoleApplication) {
            $app->controllerMap['static-url'] = [
                'class' => 'bug32\\staticUrl\\console\\StaticUrlController',
            ];
        }
    }

    protected function registerTranslations(): void
    {
        \Yii::$app->i18n->translations['static-url'] = [
            'class'          => 'yii\\i18n\\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath'       => '@staticUrl/messages',
        ];
    }
} 