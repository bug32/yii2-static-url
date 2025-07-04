<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model bug32\staticUrl\models\StaticUrl */

$this->title = 'Создать статический URL';
$this->params['breadcrumbs'][] = ['label' => 'Статические URL', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="static-url-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div> 