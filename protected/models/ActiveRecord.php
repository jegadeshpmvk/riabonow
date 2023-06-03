<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use app\behaviors\TypecastBehavior;

class ActiveRecord extends \yii\db\ActiveRecord {

    public $saveType;
    public $pagename = "";
    public $sent_by = "";

    public static function find() {
        $find = new Scope(get_called_class());
        return $find;
    }

    public function init() {
        parent::init();
        $this->pagename = $this->tableName();
    }

    public function behaviors() {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
            'typecast' => [
                'class' => TypecastBehavior::className()
            ],
        ];
    }

    public function attributeLabels() {
        return [
            'id' => 'ID',
            'created_at' => 'Created At',
            'updated_at' => 'Last Updated'
        ];
    }

    public function rules() {
        return [
            [['deleted'], 'integer'],
            [['created_at', 'updated_at', 'pagename', 'save_type', 'sent_by'], 'safe']
        ];
    }

    public function updatedAttributes($type, $changed) {
        $temp = [];
        foreach ($changed as $key => $value) {
            if ($type === 'new') {
                $val = $this->{$key};
            } else {
                $val = $changed[$key];
            }
            $temp[$key] = $val;
        }
        return $temp;
    }

    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);
        $this->sent_by = Yii::$app->user->id;
        // Remove Updated only
        if (count($changedAttributes) == 1) {
            if (isset($changedAttributes['updated_at'])) {
                return false;
            }
        }
        if (isset($this->saveType)) {
            if (strpos($this->saveType, 'create') !== false) {
                $this->addActivity($this->saveType, $this, [
                    "model_id" => $this->id,
                    "new" => $this->updatedAttributes('new', $changedAttributes)
                ]);
            } else {
                $this->addActivity($this->saveType, $this, [
                    "model_id" => $this->id,
                    "old" => $this->updatedAttributes('old', $changedAttributes),
                    "new" => $this->updatedAttributes('new', $changedAttributes)
                ]);
            }
        }
    }

    public static function getMediaOrder($ids = []) {
        $res = array();
        if ($ids) {
            foreach ($ids as $id) {
                $media = Media::find()->andWhere(['id' => trim($id), 'deleted' => 0])->one();
                if ($media) {
                    $res[] = $media;
                }
            }
        }
        return $res;
    }

    public function addActivity($action, $model, $value) {
        if (@$value["old"] != @$value["new"]) {
            $explode = explode("\\", get_class($model));
            $section = end($explode);
            if (!Yii::$app->user->isGuest) {
                //Save activity
                $m = new Activity();
                $m->user_id = Yii::$app->user->identity->id;
                $m->section = $section;
                $m->action = $this->saveType;
                $m->value = $value;
                $m->save();
            }
        }
    }

    public function getId() {
        return $this->getPrimaryKey();
    }

    public function getIcons() {
        return [
            "fa fa-facebook" => "Facebook",
            "fa fa-twitter" => "Twitter",
            "fa fa-instagram" => "Instagram",
            "fa fa-linkedin" => "Linkedin",
            "fa fa-youtube" => "Youtube",
            "fa jd" => "Just Dial",
            "fa fa-tachometer" => "Tachometer",
            "fa fa-wifi" => "Wifi",
            "fa fa-inr" => "Inr",
            "fa fa-exchange" => "Exchange",
            "fa fa-database" => "Database",
            "fa fa-headphones" => "Headphones",
            "fa fa-envelope-o" => "Email",
            "fa fa-map-marker" => "Map Marker",
            "fa fa-whatsapp" => "Whatsapp"
        ];
    }

    public function getSocial() {
        return [
            "fa fa-facebook" => "Facebook",
            "fa fa-twitter" => "Twitter",
            "fa fa-instagram" => "Instagram",
            "fa fa-linkedin" => "Linkedin",
            "fa fa-youtube" => "Youtube",
            "fa jd" => "Just Dial",
            "fa fa-whatsapp" => "Whatsapp"
        ];
    }

    public function getCategory() {
        return [
            "awards" => "Awards",
            "projects" => "Projects"
        ];
    }

}
