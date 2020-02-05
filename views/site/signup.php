<?php
/* @var $this \yii\web\View */
/* @var $signupForm \app\models\SignupForm */
use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>
<div class="registration">
    <div class="cont clfix">
        <h3>Регистрация</h3>

        <?php $form = ActiveForm::begin([
            'enableAjaxValidation' => true
        ]); ?>
        <?= $form->field($signupForm, "email", ["options" => ["class" => "fLine"], "template" => "{input}{error}"])->textInput(["class" => "inp", "placeholder" => "Введите ваш E-mail"]); ?>
        <?= $form->field($signupForm, "password", ["options" => ["class" => "fLine"], "template" => "{input}{error}"])->passwordInput(["class" => "inp", "placeholder" => "Введите ваш пароль"]); ?>
        <?= $form->field($signupForm, "repassword", ["options" => ["class" => "fLine"], "template" => "{input}{error}"])->passwordInput(["class" => "inp", "placeholder" => "Повторите пароль"]); ?>
        <?= $form->field($signupForm, "agree", ["options" => ["class" => "fLine"], "template" => "{input}{error}"])->checkbox(); ?>
        <div class="fLine"><input class="regBtn inpBtn" type="submit" value="Зарегистрироваться"></div>
        <div class="fLine socialReg" id="authchoice">
            <p>или Войдите через свой аккаунт в соцсети</p>
            <a data-popup-height="480" data-popup-width="860" title="В контакте" href="<?=Url::toRoute(['site/auth', 'authclient' => 'vk']);?>" class="auth-link vk">vk.com</a><a data-popup-height="480" data-popup-width="860" title="Facebook" href="<?=Url::toRoute(['site/auth', 'authclient' => 'facebook']);?>" class="auth-link fb">fb.com</a>
            <a data-popup-height="480" data-popup-width="860" title="Google" href="<?=Url::toRoute(['site/auth', 'authclient' => 'google']);?>" class="auth-link google"> </a>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

<?php if(false):?><script><?php endif; ?>
<?php ob_start(); ?>

    $('form#w0').on('beforeSubmit', function () {
        ga('send', { 'hitType': 'pageview', 'page': '/signup/submitted/', 'title': 'Register Submitted' });
    });

<?php $js = ob_get_contents(); ob_end_clean(); $this->registerJs($js); ?>