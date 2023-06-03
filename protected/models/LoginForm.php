<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 */
class LoginForm extends Model {

    public $email;
    public $password;
    public $type;
    public $rememberMe = true;
    private $_user = false;

    public function rules() {
        return [
            [['email', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
            ['type', 'safe']
        ];
    }

    public function attributeLabels() {
        return [
            'email' => 'Username or Email Address',
            'password' => 'Password'
        ];
    }

    public function validatePassword($attribute, $params) {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    public function login() {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        } else {
            return false;
        }
    }

    public function getUser() {
        if ($this->_user === false) {
            $this->_user = Admin::findByUsername($this->email);
        }
        return $this->_user;
    }

}
