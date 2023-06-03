<?php

use yii\helpers\Html;

?>
<h1 class="p-tl"><?= (int) $exception->statusCode ? 'HTTP '.$exception->statusCode : "Error" ?></h1>
<div class="user-upload-help" style="padding: 15px 30px; font-size: 15px;">
    <p><?= nl2br(Html::encode($exception->getMessage())) ?></p>
</div>
