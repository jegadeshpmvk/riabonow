<?php

namespace app\modules\admin;

use yii\web\AssetBundle;

class AdminAsset extends AssetBundle {

    public $sourcePath = '@app/modules/admin/assets';
    public $css = [
        'css/common.css',
        'css/font-awesome.css',
        'css/fileupload.css',
        'css/alertify.css',
        //'css/select2.min.css',
        'css/login.css',
        'css/stylesheet.css',
    ];
    public $js = [
        'js/libs/modernizr.js',
        'js/libs/alertify.js',
        'js/libs/js.cookie.js',
        'js/libs/jquery.rowgrid.js',
        'js/libs/jquery.autosize.js',
        'js/libs/jquery.redactor.js',
        'js/libs/jquery.dropdown.js',
        'js/libs/jquery.fileupload.js',
        'js/libs/select2/select2.full.min.js',
        'js/libs/jquery.transform2d.js',
        'js/libs/apexcharts.js',
        'js/gmap.js',
        'js/imagemanager.js',
        'js/block.js',
        'js/flexible.js',
        'js/script.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\jui\JuiAsset',
    ];
    public $publishOptions = [
        'forceCopy'
        => true
    ];

}
