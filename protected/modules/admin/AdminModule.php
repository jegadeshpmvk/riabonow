<?php

namespace app\modules\admin;
use yii\base\Module;

class AdminModule extends Module
{
    public $controllerNamespace = 'app\modules\admin\controllers';
    public $layout = 'main';

    public function init()
    {
    	parent::init();
    	\Yii::$app->errorHandler->errorAction = 'admin/default/error';
    }
}
