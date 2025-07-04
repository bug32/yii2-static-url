<?php

namespace bug32\staticUrl\console;

use bug32\staticUrl\components\StaticUrlRule;
use bug32\staticUrl\models\StaticUrl;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

class StaticUrlController extends Controller
{
    public function actionIndex(): int
    {
        $urls = StaticUrl::find()->all();
        if (empty($urls)) {
            $this->stdout("Статические URL не найдены.\n", Console::FG_YELLOW);
            return ExitCode::OK;
        }
        $this->stdout("Список статических URL:\n", Console::FG_GREEN);
        $this->stdout(str_repeat('-', 80) . "\n");
        foreach ($urls as $url) {
            $status = $url->status === StaticUrl::STATUS_ACTIVE ? 'Активен' : 'Неактивен';
            $this->stdout(sprintf(
                "ID: %d | URL: %s | Маршрут: %s/%s | Статус: %s\n",
                $url->id,
                $url->url,
                $url->controller,
                $url->action,
                $status
            ));
        }
        return ExitCode::OK;
    }

    public function actionClearCache(): int
    {
        StaticUrlRule::clearCache();
        $this->stdout("Кэш статических URL очищен.\n", Console::FG_GREEN);
        return ExitCode::OK;
    }

    public function actionCreate(string $url, string $controller, string $action, string $params = '{}'): int
    {
        $model = new StaticUrl();
        $model->url = $url;
        $model->controller = $controller;
        $model->action = $action;
        $model->params = $params;
        if ($model->save()) {
            StaticUrlRule::clearCache();
            $this->stdout("Статический URL создан успешно.\n", Console::FG_GREEN);
            return ExitCode::OK;
        } else {
            $this->stdout("Ошибка при создании статического URL:\n", Console::FG_RED);
            foreach ($model->errors as $attribute => $errors) {
                $this->stdout("  $attribute: " . implode(', ', $errors) . "\n", Console::FG_RED);
            }
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }

    public function actionDelete(int $id): int
    {
        $model = StaticUrl::findOne($id);
        if (!$model) {
            $this->stdout("Статический URL с ID $id не найден.\n", Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }
        if ($model->delete()) {
            StaticUrlRule::clearCache();
            $this->stdout("Статический URL удален успешно.\n", Console::FG_GREEN);
            return ExitCode::OK;
        }

        $this->stdout("Ошибка при удалении статического URL.\n", Console::FG_RED);
        return ExitCode::UNSPECIFIED_ERROR;
    }
} 