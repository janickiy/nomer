<?php
/* @var $this \yii\web\View */
/* @var $signupForm \app\models\SignupForm */
/* @var $signinForm \app\models\SigninForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = \Yii::$app->name.' - Вход по E-mail';

?>

<div class="registration">
    <div class="clfix">
        <h2>Вход на сайт</h2>

        <?php if($error = \Yii::$app->session->getFlash("error")): ?>
            <div class="error"><?=$error;?></div>
        <?php endif ;?>

        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($signinForm, "email", ["options" => ["class" => "fLine"], "template" => "{input}"])->textInput(["class" => "inp", "placeholder" => "Введите ваш E-mail"])->label("Ваш e-mail", ["class" => "of_input_text__label is_filled"]); ?>
        <?= $form->field($signinForm, "password", ["options" => ["class" => "fLine"], "template" => "{input}"])->passwordInput(["class" => "inp", "placeholder" => "Введите ваш пароль"])->label("Ваш пароль", ["class" => "of_input_text__label is_filled"]); ?>
        <div class="fLine"><input class="signinBtn inpBtn" value="Войти" type="submit"></div>
        <div class="fLine socialReg" id="authchoice">
            <p>или Войдите через свой аккаунт в соцсети</p>
            <a data-popup-height="480" data-popup-width="860" title="В контакте" href="<?=Url::toRoute(['site/auth', 'authclient' => 'vk']);?>" class="auth-link vk">vk.com</a>
            <a data-popup-height="480" data-popup-width="860" title="Facebook" href="<?=Url::toRoute(['site/auth', 'authclient' => 'facebook']);?>" class="auth-link fb">fb.com</a>
            <a data-popup-height="480" data-popup-width="860" title="Google" href="<?=Url::toRoute(['site/auth', 'authclient' => 'google']);?>" class="auth-link google"> </a>
        </div>
        <?php ActiveForm::end(); ?>

        <p style="font-weight: bold;">нет аккаунта?</p>

        <div class="fLine"><input class="signinBtn inpBtn" value="Зарегистрироваться" type="button" onclick="location.href='<?=Url::toRoute(["site/signup"]);?>'"></div>
        <br /><br />
        <a href="/remind" class="remind">Забыли пароль?</a>
    </div>
</div>