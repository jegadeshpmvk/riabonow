<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use app\behaviors\TypecastBehavior;

class Activity extends \yii\db\ActiveRecord {

    public static function tableName() {
        return '{{%activity}}';
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

    public function rules() {
        return [
            [['user_id', 'action', 'section'], 'required'],
            [['section'], 'string', 'max' => 255],
            [['value', 'created_at', 'updated_at', 'user_id'], 'safe']
        ];
    }

    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id'])->select(['_id', 'first_name', 'surname', 'type']);
    }

    public function beforeSave($insert) {
        $this->value = json_encode($this->value);
        return parent::beforeSave($insert);
    }

}
