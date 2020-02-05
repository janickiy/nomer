<?php
/* @var $this \yii\web\View */
use yii\widgets\ActiveForm;

/* @var $model \app\models\setPasswordForm */

$this->title = \Yii::$app->name.' - настройки';
?>
<div class="registration">
    <div class="cont clfix">
        <h2>Настройки</h2>

        <?php if(($success = \Yii::$app->session->getFlash("success"))): ?>
            <p class="success"><?=$success;?></p>
        <?php endif; ?>

        <?php if($error = \Yii::$app->session->getFlash("error")): ?>
            <div class="error"><?=$error;?></div>
        <?php endif ;?>

        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, "oldpassword", ["options" => ["class" => "fLine"], "template" => "{input}"])->passwordInput(["class" => "inp", "placeholder" => "Введите текущий пароль"]); ?>
        <?= $form->field($model, "password", ["options" => ["class" => "fLine"], "template" => "{input}"])->passwordInput(["class" => "inp", "placeholder" => "Введите новый пароль"]); ?>
        <?= $form->field($model, "repassword", ["options" => ["class" => "fLine"], "template" => "{input}"])->passwordInput(["class" => "inp", "placeholder" => "Подтвердите пароль"]); ?>
        <div class="fLine"><input class="button" value="Установить пароль" type="submit"></div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

