<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model vcruis\staticUrl\models\StaticUrl */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="static-url-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'controller')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'action')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'params')->textarea(['rows' => 6]) ?>
    <?= $form->field($model, 'status')->dropDownList($model->getStatusList()) ?>
    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div> 