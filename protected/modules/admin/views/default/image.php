<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<h1 class="p-tl"><?php  echo $model->isNewRecord? "Create" : "Update"; ?> Image Attribute</h1>
<div class="model-form">
	<?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'position')->dropDownList($model->cropPosition)->label('Crop image from') ?>

    <?= $form->field($model, 'alt') ?>

	<div class="buttons">
	    <?= Html::submitButton('Save', ['class' => 'fa fa-save']) ?>
	    <?= Html::a('Discard', "#", ['class' => 'fa fa-remove', 'onClick'=>'window.parent.blogIframe.close();']) ?>
	</div>

	<?php ActiveForm::end(); ?>
</div>
