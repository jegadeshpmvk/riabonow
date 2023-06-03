<?php

use yii\helpers\Html;
use yii\widgets\Spaceless;

$this->title = Yii::$app->name;

/* Remove spaces */
Spaceless::begin();

/* Render content */
$this->beginPage();
?>
<!DOCTYPE html>
<html lang="" class="">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" media="print" href="/protected/modules/admin/assets/css/stylesheet.css">
        <title><?= Html::encode($this->title) ?></title>
        
    </head>
    <body>
        <?php $this->beginBody() ?>
        <div class="viewport">
            <div class="content">
                <?= $content; ?>
            </div>
        </div>
        <?php $this->endBody() ?>
    </body>
</html>
<?php
$this->endPage();
Spaceless::end();
?>
