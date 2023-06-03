<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<h1 class="page_title">Change Password for <?= $model->username; ?></h1>
<div class="model_form">    
    <?php $form = ActiveForm::begin(['enableAjaxValidation' => true]); ?>
    <div class="_2_col_form form_widget_group">
        <?= $form->field($model, 'password')->textInput(['maxlength' => 255]) ?>
        <?= $form->field($model, 'password_repeat')->textInput(['maxlength' => 255]) ?>
    </div>
    <div class="options">
        <?= Html::submitButton('<span>Save</span>', ['class' => 'fa fa-save']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
