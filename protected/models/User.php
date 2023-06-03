<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;
use yii\helpers\ArrayHelper;

class User extends ActiveRecord implements IdentityInterface {

    public $password_repeat;

    const COOKIE_NAME = "jobcard_settings";
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    const TYPE_ADMIN = 'admin';
    const TYPE_CUSTOMER = 'customer';

    public static function tableName() {
        return '{{%user}}';
    }

    public function rules() {
        $rules = [
            [['username', 'email', 'email_hash', 'password', 'password_repeat', 'authKey', 'type'], 'string', 'max' => 255],
            [['email'], 'required'],
            [['email'], 'email'],
            [['username', 'email'], 'unique'],
            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'on' => ['password_change']],
            ['password_repeat', 'required', 'on' => ['password_change']],
            [['password'], 'required', 'on' => ['password_change']],
            ['password', 'match', 'pattern' => '/^(?=.*?[a-z])(?=.*?[0-9]).{8,}\S+$/', 'message' => 'Should contains: [A-Z] [a-z] [0-9] [#?!@$%^&*-]', 'on' => ['create', 'password_change']],
            ['password', 'string', 'min' => 8],
            [['settings', 'last_login'], 'safe']
        ];
        return ArrayHelper::merge(parent::rules(), $rules);
    }

    public function attributeLabels() {
        $labels = [
            'id' => 'ID',
            'name' => 'Name',
            'username' => 'Username',
            'email' => 'Email'
        ];
        return ArrayHelper::merge(parent::attributeLabels(), $labels);
    }

    public function afterFind() {
        $this->settings = json_decode($this->settings, true);
    }

    public function beforeSave($insert) {
        if ($this->isNewRecord) {
            $this->password = static::generatePassword($this->password);
        }

        return parent::beforeSave($insert);
    }

    /* Session Specific Functions */

    public static function setLoginTime($identity) {
        try {
            $user = $identity->findIdentity($identity->id);
            //$user->last_login = (string) strtotime(date('Y-m-d H:i:s'));
            //$user->save(false);
        } catch (Exception $e) {
            // Too many requests made to the API too quickly
        }
    }

    public static function findIdentity($id) {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null) {
        foreach (self::$users as $user) {
            if ($user['accessToken'] === $token) {
                return new static($user);
            }
        }
        return null;
    }

    public static function findByUsername($username) {
        $username = trim(strtolower($username)); //Convert to lowercase & trim spaces
        return static::find()->andWhere(['OR', ['username' => $username], ['email' => $username]])->active()->one();
    }

    public static function findByPasswordResetToken($token) {
        return static::findOne([
                    'email_hash' => $token
        ]);
    }

    public function getAuthKey() {
        return $this->authKey;
    }

    public function validateAuthKey($authKey) {
        return $this->getAuthKey() === $authKey;
    }

    public function validatePassword($password) {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }

    public static function generatePassword($password) {
        return Yii::$app->getSecurity()->generatePasswordHash($password);
    }

    public function getRowsCount() {
        return [
            10 => 10,
            15 => 15,
            20 => 20,
            25 => 25,
            30 => 30,
            35 => 35,
            40 => 40,
            45 => 45,
            50 => 50,
            60 => 60,
            70 => 70,
            80 => 80,
            90 => 90,
            100 => 100
        ];
    }

    public function getAutosaveInterval() {
        return [
            10000 => "10 seconds",
            15000 => "15 seconds",
            20000 => "20 seconds",
            25000 => "25 seconds",
            30000 => "30 seconds",
            35000 => "35 seconds",
            40000 => "40 seconds",
            45000 => "45 seconds",
            50000 => "50 seconds",
            55000 => "55 seconds",
            60000 => "1 minute"
        ];
    }

    public function getDefaultSettings() {
        return [
            'go_back' => 0,
            'list_total' => 15,
            'autosave' => 20000
        ];
    }

    public function getCookie($variable) {
        $arr = $this->defaultSettings;

        //Get from cookie
        if (isset($_COOKIE[self::COOKIE_NAME]))
            $arr = json_decode($_COOKIE[self::COOKIE_NAME], true);

        return isset($arr[$variable]) ? $arr[$variable] : "";
    }

    public function updateCookie() {
        setcookie(self::COOKIE_NAME, json_encode($this->settings), time() + 3600000, "/");
    }

    public function setCookie() {
        setcookie(self::COOKIE_NAME, $this->settings, time() + 3600000, "/");
    }

}
