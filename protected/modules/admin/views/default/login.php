<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Login';
?>
<div class="login_page">
    <div class="_table">      
        <div class="_table_cell">
            <div class="_table_cell_width">
                <div class="_flex">
                    <div class="inline_flex">
                        <div class="p_rea">
                          <?php /*  <div class="bsz">
                                <div class="bgimage" style="background-image: url(<?= Yii::getAlias("@icons") ?>/boxes.png)"></div>
                                <img src="<?= Yii::getAlias("@icons") ?>/boxes.png" alt="" />
                            </div> */ ?>
                            <div class="login_logo"><img class="logo" src="<?= Yii::getAlias("@icons") ?>/logo-light.png" /></div>                            
                        </div>
                    </div>
                    <div class="inline_flex">
                        <div class="login_form">
                            <div class="login_form_text poppins_semi_bold">Log In</div>
                            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
                            <?= $form->field($model, 'email')->textInput(['placeholder' => "Email ID", "autocomplete" => "off"])->label(false) ?> 
                            <?= $form->field($model, 'password')->passwordInput(['placeholder' => "Password"])->label(false) ?>
                            <div class="form-group">
                                <label class="has-checkbox" for="client-login-rememberme">
                                    <input type="checkbox" id="client-login-rememberme" name="ClientLogin[rememberMe]" value="1">
                                    <span class="square"></span><span>Remember Me</span>
                                </label>
                            </div>
                            <div class="form-group has-submit">
                                <?= Html:: submitButton('<span>Log In</span>', ['class' => 'btn btn__block btn__primary', 'name' => 'login-button']) ?>
                            </div>
                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>