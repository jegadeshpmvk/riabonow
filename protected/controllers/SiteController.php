<?php

namespace app\controllers;

use app\components\Controller;
use app\models\CustomPage;

class SiteController extends Controller
{

    public function actionIndex()
    {
        $title = '';
        if (trim(@$model->name) != '') {
            $title = @$model->name . ' | Specta Fiber';
        }

        $this->setupMeta(@$model->meta_tag, $title);
        return $this->render('index', [
            "model" => "",
        ]);
    }
}
