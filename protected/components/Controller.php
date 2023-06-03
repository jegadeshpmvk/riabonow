<?php

namespace app\components;

use Yii;
use yii\helpers\Url;

class Controller extends \yii\web\Controller {

    public $onlyContent = false;
    public $meta = ['title' => '', 'canonical' => '', 'keywords' => '', 'description' => '', 'social' => '', 'share' => []];
    public $tab;
    public $htmlClass = [];

    public function init() {
        parent::init();

        if (Yii::$app->request->isAjax)
            $this->layout = 'empty';
        else if (!YII_ENV_DEV && $this->module->id == "basic") {
            Yii::$app->assetManager->bundles = require(Yii::getAlias('@webroot') . '/../assets/MinifiedAsset.php');
        }
    }

    public function setupMeta($model, $title = '', $override = []) {
        $this->meta['title'] = $title ? $title : @$model['title'];
        $this->meta['description'] = @$model['description'];
        $this->meta['keywords'] = @$model['keywords'];

        if (!$this->isEmpty($model)) {
            //Override with title entered in admin
            if (strip_tags($model->title) != "")
                $this->meta['title'] = strip_tags($model->title);

            //Description
            $this->meta['description'] = trim(preg_replace('/\s\s+/', ' ', strip_tags($model->description)));

            //Set share image
            $image = Yii::$app->file->getUrl(@$model->share_image, "original", true);
            if (!isset($override['image']) && $image != "")
                $override['image'] = $image;
        }

        $this->setupShare($override);
    }

    public function setupShare($override) {
        //Set default title on empty
        if (trim($this->meta['title']) == "")
            $this->meta['title'] = 'Riabonow | Madurai';

        $this->meta['share'] = [
            'url' => Yii::$app->request->absoluteUrl,
            'name' => Yii::$app->name,
            'title' => trim($this->meta['title']) == "" ? $title : $this->meta['title'],
            'description' => $this->meta['description'],
            'keywords' => $this->meta['keywords'],
            'image' => Url::to("@icons", true) . "/social-share.png"
        ];

        foreach ($override as $key => $value) {
            $this->meta['share'][$key] = $value;
        }
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
