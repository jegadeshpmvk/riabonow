<?php

namespace app\models;

use Yii;

class Media extends ActiveRecord {

    public static function tableName() {
        return '{{%media}}';
    }

    public function rules() {
        return [
            [['type', 'folder', 'orgname', 'name', 'extension'], 'required'],
            [['orgname', 'name'], 'string'],
            [['width', 'height'], 'integer'],
            [['type', 'folder', 'extension', 'position', 'alt'], 'string', 'max' => 255],
            [['updated_at', 'created_at'], 'safe']
        ];
    }

    public function attributeLabels() {
        return [
            'id' => 'ID',
            'alt' => 'ALT Tag',
        ];
    }

    public function getCropPosition() {
        return [
            '50% 50%' => 'Center',
            '0 0' => 'Top Left',
            '50% 0' => 'Top Center',
            '100% 0' => 'Top Right',
            '0 100%' => 'Bottom Left',
            '50% 100%' => 'Bottom Center',
            '100% 100%' => 'Bottom Right',
        ];
    }

    public function fields() {
        $fields = [
            'url'
        ];
        return $fields;
    }

    public function geturl($width = 0, $height = 0) {
        if ($this->extension == 'svg') {
            return Yii::$app->file->getUrl($this, 'original', true);
        } else {
            return Yii::$app->file->getUrl($this, [$width, $height], true);
        }
    }

}
