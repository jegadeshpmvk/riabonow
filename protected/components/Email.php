<?php

namespace app\components;

use Yii;

class Email extends \yii\base\Component {

    public function admin($data = []) {
        $message = Yii::$app->mailer->compose('@app/mail/email', ['data' => $data, 'name' => isset($data['name']) ? $data['name'] : 'Admin'])
                ->setFrom([Yii::$app->params['fromEmail'] => Yii::$app->name])
                ->setTo(isset($data['email']) ? $data['email'] : Yii::$app->params['toEmail'])
                ->setSubject($data['subject']);
        if (isset($data['attachment']) && $data['attachment'] != "") {
            $message->attach($data['attachment']);
        }
        if (isset($data['ccemail']) && $data['ccemail'] != "") {
            $message->setCc($data['ccemail']);
        }
        $message->send();
    }

    public function customer($data = []) {
        $message = Yii::$app->mailer->compose('@app/mail/email', ['data' => $data, 'name' => $data['name']])
                ->setFrom([Yii::$app->params['fromEmail'] => Yii::$app->name])
                ->setTo($data['email'])
                ->setSubject($data['subject']);
        if (isset($data['attachment']) && $data['attachment'] != "") {
            $message->attach($data['attachment']);
        }
        if (isset($data['ccemail']) && $data['ccemail'] != "") {
            $message->setCc($data['ccemail']);
        }
        $message->send();
    }

}
