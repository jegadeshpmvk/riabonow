<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Spaceless;
use app\modules\admin\AdminAsset;

AdminAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <link rel="shortcut icon" href="<?= Url::to('@icons') ?>/favicon.ico?v=1.2" type="image/ico">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Heebo:400,500,700%7cRajdhani:400,500,600,700&display=swap">
        <link rel="stylesheet" type="text/css" media="print" href="/protected/modules/admin/assets/css/stylesheet.css">
        <?= Html::csrfMetaTags() ?>
        <title><?= Yii::$app->name ?></title>
        <?php $this->head(); ?>
        <script type="text/javascript">var cookie_prefix = "<?= Yii::$app->name ?>";</script>
    </head>
    <body>
        <?php $this->beginBody() ?>
        <div id="loading-screen" class="loading">
            <div class="v-aln-wr">
                <div class="v-aln">
                    <div id="noscript" class="aln-c">Please enable JavaScript in your browser to access this website.</div>
                    <div id="load-spinner" class="load-text">
                        <div class="left"></div>
                        LOADING
                    </div>
                </div>
            </div>
        </div>  
        <script type="text/javascript">
            /*<![CDATA[*/
            var elem = document.getElementById("loading-screen");
            elem.parentNode.removeChild(elem);
            var _app_prefix = "<?= Yii::getAlias('@prefix') ?>";
            /*]]>*/
        </script>
        <?php if (!Yii::$app->controller->onlyContent) { ?>
            <div class="header">
                <a class="burger_menu"><span class="lines"></span></a>
                <div class="logo">
                    <a href="/" class="ab-item" target="_blank">
                        <img src="<?= Yii::getAlias('@icons') ?>/logo1.png" alt="">
                    </a>
                </div>
            </div>
            <div class="panel left">
                <div class="panel_left_div"></div>
                <?php if (!Yii::$app->user->isGuest) echo $this->render('menu'); ?>
            </div>
            <div class="panel right">
                <div class="content">
                    <?php
                }
                Spaceless::begin();
                echo $content;
                Spaceless::end();
                ?>
                <?php if (!Yii::$app->controller->onlyContent) { ?>
                </div>
            </div>
            <!-- Search Bar (Used to search gridview) -->
            <div class="bar search-bar">
                <?= Html::a('<span>Back</span>', NULL, ['class' => 'btn fa fa-arrow-left']) ?>       
                <div class="bar-options">
                    <?= Html::a('<span>Go</span>', NULL, ['class' => 'btn fa fa-search']) ?>
                    <?= Html::a('<span>Reset</span>', NULL, ['class' => 'btn fa fa-refresh']) ?>
                </div>
            </div>
            <!-- Sorting Bar (Used to sort the rows in gridview) -->
            <div class="bar sort-bar">
                <?= Html::a('<span>Back</span>', NULL, ['class' => 'btn fa fa-arrow-left']) ?>
                <div class="bar-options">
                    <?= Html::a('<span>Save</span>', NULL, ['class' => 'btn fa fa-save']) ?>
                </div>
            </div>
            <!-- Image Library Bar (Allow users to select images from a library) -->
            <div class="bar image-library-bar">
                <?= Html::a('<span>Back</span>', NULL, ['class' => 'btn fa fa-arrow-left']) ?>
                <h1><span>Media Library</span></h1>
                <div class="bar-options">
                    <?= Html::a('<span>Select</span>', NULL, ['class' => 'btn fa fa-check']) ?>
                </div>
            </div>
            <div class="image-library">
                <div class="images-container"></div>
            </div>
        <?php } ?>
        <?php $this->endBody() ?>
        <?php foreach (Yii::$app->session->getAllFlashes() as $key => $message) { ?> 
            <script>
                alertify.<?= $key; ?>("<?= $message; ?>");
            </script>
        <?php } ?>
    </body>
</html>
<?php $this->endPage() ?>
