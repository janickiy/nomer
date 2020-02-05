<?php
/* @var $this \yii\web\View */
use yii\widgets\ActiveForm;

/* @var $model \app\models\newPasswordForm */

$this->title = \Yii::$app->name.' - установка нового пароля пароля';
?>
<div class="registration">
    <div class="cont clfix">
        <h2>Установка нового пароля</h2>

        <?php if($error = \Yii::$app->session->getFlash("error")): ?>
            <div class="error"><?=$error;?></div>
        <?php endif ;?>

        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, "password", ["options" => ["class" => "fLine"], "template" => "{input}"])->passwordInput(["class" => "inp", "placeholder" => "Введите новый пароль"]); ?>
        <?= $form->field($model, "repassword", ["options" => ["class" => "fLine"], "template" => "{input}"])->passwordInput(["class" => "inp", "placeholder" => "Подтвердите пароль"]); ?>
        <div class="fLine"><input class="signinBtn inpBtn" value="Установить пароль" type="submit"></div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

