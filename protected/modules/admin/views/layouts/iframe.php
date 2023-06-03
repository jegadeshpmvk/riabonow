<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\admin\AdminAsset;

AdminAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head(); ?>
        <style type="text/css">
            body {
                background-color: transparent;
            }
            .box-wrapper {
                position: absolute;
                width: 100%;
                height: 100%;
                white-space: nowrap;
                text-align: center;
            }
            .box-wrapper::after {
                content: "";
                display: inline-block;
                width: 1px;
                height: 100%;
                vertical-align: middle;
            }
            .box {
                background: white;
                box-shadow: 0 0 14px rgba(0,0,0,.24),0 14px 28px rgba(0,0,0,.48);
                max-width: 700px;
                min-width: 240px;
                display: inline-block;
                width: 80%;
                white-space: normal;
                vertical-align: middle;
                margin-bottom: 50px;
                text-align: left;
            }
            .box h1, .box h2 {
                display: none;
            }
            .box .model-form {
                padding: 5px 30px 20px;
            }
        </style>
    </head>
    <body>
        <?php $this->beginBody() ?>
        <div class="box-wrapper">
            <div class="box"><?= $content ?></div>
        </div>
        <!-- Image Library Bar (Allow users to select images from a library) -->
        <div class="bar image-library-bar">
            <?= Html::a('Back', NULL, ['class' => 'btn fa fa-arrow-left']) ?>
            <h1><span>Media Library</span></h1>
            <div class="bar-options">
                <?= Html::a('Select', NULL, ['class' => 'btn fa fa-check']) ?>
            </div>
        </div>
        <div class="image-library">
            <div class="images-container"></div>
        </div>
        <div class="bar smugmug-library-bar">
            <?= Html::a('Back', NULL, ['class' => 'btn fa fa-arrow-left']) ?>
            <h1><span>Smugmug Library</span></h1>
            <div class="bar-options">
                <?= Html::a('Select', NULL, ['class' => 'btn fa fa-check']) ?>
            </div>
        </div>
        <div class="smugmug-library">
            <div class="smugmug-container"></div>
        </div>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
