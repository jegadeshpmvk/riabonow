<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Spaceless;
use app\assets\AppAsset;

/* Register asset bundle */

AppAsset::register($this);
$this->title = Yii::$app->name;

/* Load required models & variables */


/* Remove spaces */
Spaceless::begin();

/* Render content */
$this->beginPage();
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="<?= implode(" ", Yii::$app->controller->htmlClass) ?>">

<head>
    <meta charset="<?= Yii::$app->charset ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" contents="noarchive" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Heebo:400,500,700%7cRajdhani:400,500,600,700&display=swap">
    <link rel="shortcut icon" href="<?= Yii::getAlias('@icons') ?>/favicon.ico?v=1.2" type="image/ico">
    <?= $this->render('sections/modernizr') ?>
    <?= Html::csrfMetaTags() ?>
    <?= $this->render('sections/meta'); ?>
    <?php $this->head() ?>
    <script type="text/javascript">
        var _app_prefix = "<?= Yii::getAlias('@prefix') ?>";
    </script>
    <?php
    //Google Analytics code
    if (!YII_ENV_DEV) {
    ?>
    <?php } ?>
</head>

<body>
    <?php $this->beginBody() ?>
    <div class="loader-line">
        <div></div>
    </div>
    <div class="viewport">
        <?= $this->render('sections/header') ?>
        <div class="content">
            <?= $content; ?>
        </div>
        <?= $this->render('sections/footer'); ?>
    </div>
    <?php $this->endBody(); ?>
</body>

</html>
<?php
$this->endPage();
Spaceless::end();
?>