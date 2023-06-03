<?php

namespace app\modules\admin\components;


use Yii;
use yii\widgets\ActiveForm;

class Controller extends \yii\web\Controller {

    public $onlyContent = false;
    public $tab;
    public $htmlClass = ['grid_view'];
    public $meta = ['title' => '', 'canonical' => '', 'keywords' => '', 'description' => '', 'social' => '', 'share' => [], 'tracking' => ''];

    public function init() {
        parent::init();

        if (Yii::$app->request->isAjax)
            $this->layout = 'empty';
    }

    public function redirectCheck($url, $statusCode = 302) {
        if (!Yii::$app->user->isGuest) {
            $stay = Yii::$app->user->identity->getCookie('go_back');
            if ($stay)
                return parent::redirect(Yii::$app->request->absoluteUrl);
        }

        return parent::redirect($url);
    }

    public function isEmpty($model) {
        if (@$model->title == "" && @$model->description == "")
            return true;

        return false;
    }

    public function validationError($model) {
        Yii::$app->response->statusCode = 400;
        return ActiveForm::validate($model);
    }

}
