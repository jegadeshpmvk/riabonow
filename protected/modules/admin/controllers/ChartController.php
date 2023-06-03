<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\OptionChain;
use app\modules\admin\components\Controller;
use yii\web\NotFoundHttpException;

class ChartController extends Controller
{
       public $tab = "chart";


    public function actionIndex()
    {
        return $this->render('index');
    }
}