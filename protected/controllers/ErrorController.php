<?php

namespace app\controllers;

use Yii;
use app\components\Controller;

class ErrorController extends Controller {

    public function actionIndex() {
        $exception = Yii::$app->errorHandler->exception;
        if ($exception !== null) {
            return $this->render('index', [
                        'exception' => $exception
            ]);
        }
    }

}
