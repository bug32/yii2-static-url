<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model vcruis\staticUrl\models\StaticUrl */

$this->title = 'Обновить статический URL: ' . $model->url;
$this->params['breadcrumbs'][] = ['label' => 'Статические URL', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->url, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Обновить';
?>
<div class="static-url-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div> 