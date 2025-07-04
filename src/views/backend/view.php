<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model bug32\staticUrl\models\StaticUrl */

$this->title = $model->url;
$this->params['breadcrumbs'][] = ['label' => 'Статические URL', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="static-url-view">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Обновить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите удалить этот элемент?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'url:url',
            'controller',
            'action',
            'params:ntext',
            [
                'attribute' => 'status',
                'value' => $model->getStatusList()[$model->status] ?? 'Неизвестно',
            ],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>
</div> 