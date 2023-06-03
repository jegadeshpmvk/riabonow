<?php

namespace app\extended;

use Yii;

class ActiveRecord extends \yii\db\ActiveRecord {

    public function getEmbeddedDocs() {
        return [];
    }

    public function init() {
        parent::init();

        foreach ($this->embeddedDocs as $settingsArray) {
            $attribute = $settingsArray[0];
            if (@$settingsArray['type'] == "array") {
                $this->$attribute = [];
            } else {
                $this->$attribute = $this->setupModel($attribute, $settingsArray);
            }
        }
    }

    public function afterFind() {
        parent::afterFind();

        //convert embedded docs to models
        foreach ($this->embeddedDocs as $settingsArray) {
            $attribute = $settingsArray[0];
            if (@$settingsArray['type'] == "array") {
                if (!is_array($this->$attribute))
                    $this->$attribute = (array) $this->$attribute;

                $temp = [];
                foreach ($this->$attribute as $arr) {
                    $temp[] = $this->setupModel($attribute, $settingsArray, $arr);
                }
                $this->$attribute = $temp;
            } else {
                $this->$attribute = $this->setupModel($attribute, $settingsArray);
            }
        }
    }

    public function beforeSave($insert) {
        //embed docs before save
        foreach ($this->embeddedDocs as $settingsArray) {
            $attribute = $settingsArray[0];
            if (@$settingsArray['type'] == "array") {
                $temp = [];
                foreach ($this->$attribute as $obj) {
                    if (is_object($obj))
                        $temp[] = $obj->toArray();
                }
                $this->$attribute = $temp;
            } else if ($attribute != "" && is_object($this->$attribute))
                $this->$attribute = $this->$attribute->toArray();
        }

        return parent::beforeSave($insert);
    }

    public function load($data, $formName = null) {
        $valid = true;

        if (Yii::$app->request->isPost) {
            foreach ($this->embeddedDocs as $settingsArray) {
                $attribute = $settingsArray[0];
                if (@$settingsArray['type'] == "array") {
                    $temp = [];
                    if (isset($_POST[$settingsArray['model']][0])) {
                        foreach ($_POST[$settingsArray['model']] as $item) {
                            $temp[] = $this->setupModel($attribute, $settingsArray, $item);
                        }
                    }
                    $this->$attribute = $temp;
                } else if (is_object($this->$attribute) && !$this->$attribute->load(Yii::$app->request->post()))
                    $valid = false;
            }
        }

        return parent::load($data, $formName) && $valid;
    }

    public function validate($attributeNames = null, $clearErrors = true) {
        $valid = true;
        //convert embedded docs to models
        foreach ($this->embeddedDocs as $settingsArray) {
            $attribute = $settingsArray[0];
            if (@$settingsArray['type'] == "array") {
                continue;
            }

            if (is_object($this->$attribute) && !$this->$attribute->validate())
                $valid = false;
        }

        return parent::validate($attributeNames, $clearErrors) && $valid;
    }

    private function setupModel($attribute, $settings, $post = false) {
        $ns = trim(@$settings['namespace']);
        $modelName = $ns . $settings['model'];
        $record = new $modelName();
        if ($post === false)
            $record->attributes = $this->$attribute;
        else
            $record->attributes = $post;
        return $record;
    }

    public function getEmbedDocModel($attribute) {
        foreach ($this->embeddedDocs as $settingsArray) {
            $attr = $settingsArray[0];
            if ($attr == $attribute) {
                $ns = (new \ReflectionObject($this))->getNamespaceName();
                return $ns . '\\' . $settingsArray['model'];
                break;
            }
        }

        return false;
    }

}
