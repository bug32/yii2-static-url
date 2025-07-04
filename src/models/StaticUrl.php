<?php

namespace bug32\staticUrl\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * Static URL model
 *
 * @property int $id
 * @property string $url
 * @property string $controller
 * @property string $action
 * @property string|null $params
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @package bug32\staticUrl\models
 */
class StaticUrl extends ActiveRecord
{
    public const STATUS_ACTIVE   = 10;
    public const STATUS_INACTIVE = 0;
    public const DEFAULT_PATTERN = '/^[a-z0-9\-_\/]+$/';

    public static function tableName(): string
    {
        return '{{%static_urls}}';
    }

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules(): array
    {
        $pattern = \Yii::$app->getModule('static-url')->urlValidationPattern ?? self::DEFAULT_PATTERN;
        return [
            [['url', 'controller', 'action'], 'required'],
            [['params'], 'string'],
            [['status'], 'integer'],
            [['url'], 'string', 'max' => 255],
            [['controller', 'action'], 'string', 'max' => 100],
            [['url'], 'unique'],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
            [['params'], 'default', 'value' => '{}'],
            [['url'], 'match', 'pattern' => $pattern, 'message' => 'URL может содержать только латинские буквы, цифры, дефисы, подчеркивания и слеши'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'url' => 'URL',
            'controller' => 'Контроллер',
            'action' => 'Действие',
            'params' => 'Параметры (JSON)',
            'status' => 'Статус',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
        ];
    }

    public function getParamsArray(): array
    {
        if (empty($this->params)) {
            return [];
        }
        try {
            return Json::decode($this->params) ?: [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function setParamsArray(array $params): void
    {
        $this->params = Json::encode($params);
    }

    public static function getActiveUrls(): array
    {
        return static::find()
            ->where(['status' => self::STATUS_ACTIVE])
            ->asArray()
            ->all();
    }

    public static function findByPath(string $path): ?self
    {
        return static::find()
            ->where(['url' => $path, 'status' => self::STATUS_ACTIVE])
            ->one();
    }

    public function getRoute(): string
    {
        return $this->controller . '/' . $this->action;
    }

    public function search($params): \yii\data\ActiveDataProvider
    {
        $query = StaticUrl::find();
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
        ]);
        $query->andFilterWhere(['like', 'url', $this->url])
            ->andFilterWhere(['like', 'controller', $this->controller])
            ->andFilterWhere(['like', 'action', $this->action]);
        return $dataProvider;
    }

    public static function getStatusList(): array
    {
        return [
            self::STATUS_INACTIVE => 'Неактивен',
            self::STATUS_ACTIVE => 'Активен',
        ];
    }
} 