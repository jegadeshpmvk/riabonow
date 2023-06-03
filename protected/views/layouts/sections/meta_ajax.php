<?php
	$meta = Yii::$app->controller->meta;
?>
<div class="ajaxTitle">
	<?= ($meta['title'] == "" ? Yii::$app->name : \yii\helpers\Html::encode($meta['title'])) ?>
</div>