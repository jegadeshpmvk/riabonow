<?php

namespace app\assets;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle {

    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/dev/libraries.css',
        'css/dev/style.css',
        'css/dev/custom.css',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\validators\ValidationAsset',
        'yii\widgets\ActiveFormAsset',
        'yii\grid\GridViewAsset',
    ];

}
