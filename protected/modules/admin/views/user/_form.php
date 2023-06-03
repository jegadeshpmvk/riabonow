<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$form = ActiveForm::begin();
?>
<h1 class="page_title"><?php echo $model->isNewRecord ? "Create" : "Update"; ?> User</h1>
<div class="model_form widgets">
    <h1 class="widget__title">User details</h1>
    <div class="widgets__content">
        <div class="_3_col_form form_widget_group">
            <?= $form->field($model, 'username')->textInput(['required' => 'required', 'maxlength' => 255]) ?>
            <?= $form->field($model, 'email')->textInput(['required' => 'required', 'maxlength' => 255]) ?>
        </div>
        <?php if ($model->isNewRecord) { ?>
            <div class="_2_col_form form_widget_group">
                <?= $form->field($model, 'password')->textInput(['required' => 'required', 'maxlength' => 255]) ?>
                <?= $form->field($model, 'password_repeat')->textInput(['required' => 'required', 'maxlength' => 255]) ?>
            </div>
        <?php } ?>
    </div>
</div>
<div class="options">
    <?php
    if (!$model->isNewRecord) {
        echo Html::a('<span>Reset Password</span>', ['user/password', 'id' => $model->id], ['class' => 'fa fa-unlock']);
    }
    ?>
    <?= Html::submitButton('<span>Save</span>', ['class' => 'fa fa-save']) ?>
</div>

<?php ActiveForm::end(); ?>
