<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="middle-wrap-abs login_page">
    <div class="middle">
        <div class="login_page_width">
            <a href="<?= Url::home() ?>" class="logo_a">
                <img class="logo" src="<?= Yii::getAlias("@icons") ?>/logo.png" />
            </a>        
            <div class="message"><?= !empty($data) ? $data['message'] : 'Please enter your username or email address. You will receive a link to create a new password via email.' ?></div>
            <div class="login_form">
                <?php $form = ActiveForm::begin(['id' => 'forgot-password']); ?>
                <?= $form->field($model, 'email')->textInput() ?>
                <div class="form-group has-submit align_r">
                    <?= Html::submitButton('<span>Get New Password</span>', ['class' => 'button button-primary', 'name' => 'login-button']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
            <div class="lost_back">
                <div class="">
                    <a class="lost" href="<?= Url::to('/admin') ?>">Log in</a>
                </div>
                <div class="">
                    <a href="<?= Url::home() ?>" class="back">← Back to Selvalakshmi Packaging</a>
                </div>
            </div>
        </div>
    </div>
</div>
