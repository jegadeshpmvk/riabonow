<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<h1 class="page_title">Settings</h1>
<div class="model_form">    
    <?php $form = ActiveForm::begin(); ?>
    <div class="_3_col_form form_widget_group">
        <?= $form->field($model, 'settings[go_back]')->dropDownList([0 => "Go back to list", 1 => "Stay on the same page"])->label("What should happen when you save a page?") ?>
        <?= $form->field($model, 'settings[list_total]')->dropDownList($model->rowsCount)->label("No of rows to be displayed in a list") ?>
        <?= $form->field($model, 'settings[autosave_timer]')->dropDownList($model->autosaveInterval)->label("Autosave content for every") ?>
    </div>
    <div class="options">
        <?= Html::submitButton('<span>Save</span>', ['class' => 'fa fa-save']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
