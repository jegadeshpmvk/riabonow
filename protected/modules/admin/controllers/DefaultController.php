<?php

namespace app\modules\admin\controllers;

use Yii;
use app\modules\admin\components\Controller;
use app\models\LoginForm;
use app\models\ChangePassword;
use app\models\Admin;
use app\models\Media;

class DefaultController extends Controller
{

    public function actionIndex()
    {
        return $this->redirect(['option-chain/index']);
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest)
            return $this->redirect(['index']);

        $model = new LoginForm();
        $model->type = 'admin';
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            Yii::$app->user->identity->updateCookie();
            return $this->redirect(['option-chain/index']);
        } else {
            $this->onlyContent = true;
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionOrder($name)
    {
        if (isset($_POST['items']) && is_array($_POST['items'])) {
            $i = 1;
            $model = "app\\models\\" . $name;
            foreach ($_POST['items'] as $item) {
                if ($rec = $model::findOne($item)) {
                    $rec->order = $i;
                    $rec->save();
                    ++$i;
                }
            }
            echo "success";
        } else
            echo "failed";
    }

    public function actionMeta()
    {
        $meta = "app\\models\\MetaTags";
        if (isset($_GET['page']) && isset($_GET['pageid'])) {
            $model = $meta::findOne([
                'page' => $_GET['page'],
                'page_id' => $_GET['pageid']
            ]);
        } else
            $model = $meta::findOne(['page' => $_GET['page']]);

        if (!$model) {
            $model = new $meta();
            $model->page = $_GET['page'];
            $model->page_id = isset($_GET['pageid']) ? $_GET['pageid'] : NULL;
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(Yii::$app->request->referrer);
        }

        $this->tab = $model->page;
        return $this->render('meta', array(
            'model' => $model,
        ));
    }

    public function actionForgot()
    {
        if (!Yii::$app->user->isGuest)
            return $this->redirect(['index']);

        $data = [];
        $model = new ChangePassword();
        $model->scenario = 'resetEmail';

        $error = Yii::$app->request->get("error", false);
        if ($error !== false) {
            $data = [
                "class" => "error",
                "message" => $error
            ];
        }
        $this->onlyContent = true;
        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            } else if ($model->validate()) {
                $model->sendEmail();
                $data = [
                    "class" => "success",
                    "message" => "Reset password link has been sent to " . $model->email
                ];
                $model->email = NULL;
            }
        }

        return $this->render('forgot', [
            'model' => $model,
            'data' => $data
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect(['index']);
    }

    public function actionClear()
    {
        Yii::$app->cache->flush();
        Yii::$app->session->setFlash('success', 'Cache was cleared successfully.');
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionError()
    {
        $exception = Yii::$app->errorHandler->exception;
        if ($exception !== null) {
            return $this->render('error', ['exception' => $exception]);
        }
    }

    public function actionImage()
    {
        $id = Yii::$app->request->get('id');
        $model = Media::findOne($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->layout = "empty";
            $script = '<script type="text/javascript">window.parent.blogIframe.close();</script>';
            echo $script;
            return;
        }

        $this->layout = "iframe";
        return $this->render('image', ['model' => $model]);
    }

    public function actionResetPassword($id)
    {
        $model = new ChangePassword();
        $model->scenario = 'reset';
        $model->token = $id;
        $this->onlyContent = true;
        $data = [
            "class" => "sucess",
            "message" => "You can change your old password"
        ];

        $user = Admin::find()->where(['email_hash' => $model->token])->active()->one();
        if (!$user)
            return $this->redirect(['forgot', 'error' => "Reset password link has expired"]);

        if ($user && $model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            } else if ($model->validate()) {
                $model->savePassword($user);
                return $this->redirect(['login']);
            } else {
                $data = [
                    "class" => "error",
                    "message" => "Error in saving password"
                ];
            }
        }

        return $this->render('reset', [
            'model' => $model,
            'user' => $user,
            'data' => $data
        ]);
    }
}
