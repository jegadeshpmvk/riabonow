<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<h1 class="p-tl">SEO for <?= ucfirst($model->page); ?> Page</h1>
<div class="model-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput() ?>

    <?= $form->field($model, 'keyword')->textArea() ?>

    <?= $form->field($model, 'desc')->textArea() ?>

    <div class="options">
        <?= Html::submitButton('Save', ['class' => 'fa fa-save']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
