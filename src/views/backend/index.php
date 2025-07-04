<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel bug32\staticUrl\models\StaticUrl */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Статические URL';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="static-url-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Создать статический URL', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'url:url',
            'controller',
            'action',
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    return $model->getStatusList()[$model->status] ?? 'Неизвестно';
                },
                'filter' => $searchModel->getStatusList(),
            ],
            'created_at:datetime',
            'updated_at:datetime',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div> 