<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\Admin;
use app\modules\admin\models\AdminSearch;
use app\modules\admin\components\Controller;
use yii\web\NotFoundHttpException;

class UserController extends Controller {

    public $tab = "user";

    public function behaviors() {
        return require(__DIR__ . '/../filters/LoginCheck.php');
    }

    public function actionIndex() {
        $searchModel = new AdminSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    protected function renderForm($model) {
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'User details were saved successfully.');
                return $this->redirectCheck(['index']);
            } else
                Yii::$app->session->setFlash('error', "Please fix the errors.");
        }

        return $this->render('_form', [
                    'model' => $model
        ]);
    }

    public function actionCreate() {
        $model = new Admin();
        $model->type = 'admin';
        $model->saveType = 'created';
        return $this->renderForm($model);
    }

    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $model->saveType = 'updated';
        return $this->renderForm($model);
    }

    public function actionDelete($id, $value) {
        $model = $this->findModel($id);
        $model->deleted = (int) $value;
        if ($value == 1) {
            $model->saveType = 'deleted';
        } else {
            $model->saveType = 'restored';
        }
        $model->save(false);
        return $this->redirect(\Yii::$app->request->referrer);
    }

    public function actionPassword($id) {
        $model = Admin::findOne($id);
        $model->scenario = 'password_change';
        $model->password = '';
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $model->saveType = 'password changed';
                $model->password = Admin::generatePassword($model->password);
                if ($model->save(false)) {
                    Yii::$app->session->setFlash('success', 'Password was saved successfully.');
                    return $this->redirect(['password', 'id' => $model->id]);
                }
            }
        }
        return $this->render('password', [
                    'model' => $model,
        ]);
    }

    public function actionSettings() {
        $model = Admin::findOne(Yii::$app->user->id);
        
        if ($model->load(Yii::$app->request->post()) && $model->save(false)) {
            $model->setCookie();
            Yii::$app->session->setFlash('success', 'Settings were saved successfully.');
            return $this->redirect(['settings']);
        }

        $this->tab = "settings";
        return $this->render('settings', [
                    'model' => $model,
        ]);
    }

    protected function findModel($id) {
        if (($model = Admin::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
