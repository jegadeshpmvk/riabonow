<?php

namespace app\models;

use Yii;
use yii\base\Model;

class ChangePassword extends Model {

    public $email;
    public $type;
    public $password;
    public $password_old;
    public $password_repeat;
    public $token;
    private $_user = false;

    public function init() {
        $this->type = "admin";
    }

    public function rules() {
        return [
            //Email reset link
            ['email', 'required', 'on' => 'resetEmail'],
            ['email', 'email'],
            ['email', 'checkEmail', 'on' => 'resetEmail'],
            //Update password
            [['password', 'password_repeat', 'password_old'], 'required', 'on' => 'update'],
            ['password_old', 'validatePassword', 'on' => 'update'],
            //Reset password
            [['password_repeat', 'password', 'token'], 'required', 'on' => 'reset'],
            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'on' => ['update', 'reset'], 'message' => 'Confirm Password must match the New Password'],
            ['password_repeat', 'resetPassword', 'on' => 'reset'],
            //Password Security
            ['password', 'match', 'pattern' => '#^[0-9A-Za-z]+$#', 'message' => 'Only the following characters are allowed: 0-9 A-Z a-z'],
            ['password', 'string', 'min' => 6],
            [['type'], 'safe']
        ];
    }

    public function attributeLabels() {
        return [
            'password_old' => 'Old Password',
            'password' => 'New Password',
            'password_repeat' => 'Confirm New Password',
        ];
    }

    public function validatePassword($attribute, $params) {
        if (!$this->hasErrors())
        {
            $user = Admin::findOne((int) Yii::$app->user->id);
            if ($user)
            {
                if (!$user->validatePassword($this->password_old))
                    $this->addError($attribute, 'Incorrect password.');
            } else
                $this->addError($attribute, 'User not found.');
        }
    }

    public function savePassword($user = false) {
        if ($user === false)
        {
            $user = Admin::findOne((string) Yii::$app->user->id);
        }
        if ($user)
        {
            $user->password = $user::generatePassword($this->password);
            $user->email_hash = NULL;
            if (!$user->save(false))
                $this->addError($attribute, 'Error in saving the new password. Please try again.');
        }
    }

    public function resetPassword($attribute, $params) {
        if (!$this->hasErrors())
        {
            $user = Admin::findOne(['email_hash' => $this->token]);
            if ($user)
            {
                $user->password = User::generatePassword($this->password);
                $user->email_hash = NULL;
                if (!$user->save())
                    $this->addError($attribute, 'Error in saving the new password. Please try again.');
            } else
                $this->addError($attribute, 'Password reset link has expired. Please <a href="' . \yii\helpers\Url::to(['forgot']) . '">reset it</a> again.');
        }
    }

    public function checkEmail($attribute, $params) {
        if (!$this->hasErrors())
        {
            $user = Admin::findOne(['email' => $this->email, 'deleted' => 0]);
            if (!$user)
                $this->addError($attribute, 'No user was found with email "' . $this->email . '".');
        }
    }

    public function sendEmail() {
        $user = Admin::findOne(['email' => $this->email]);
        //Reset Hash
        $user->email_hash = md5(time() . Yii::$app->params['salt'] . $user->id . $user->email);
        $user->save(false);
        //Send email
        Yii::$app->mailer->compose('email', ['name' => 'Customer', 'data' => ['title' => 'Password Reset', 'user' => $user]])
                ->setFrom(Yii::$app->params['fromEmail'])
                ->setTo($user->email)
                ->setSubject('Password Reset')
                ->send();
    }

}
