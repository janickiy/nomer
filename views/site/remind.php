<?php
/* @var $this \yii\web\View */
/* @var $remindForm \app\models\RemindForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = \Yii::$app->name.' - восстановление пароля';

?>

<div class="registration">
    <div class="cont clfix">
        <h2>Восстановление пароля</h2>
        <?php if($message = \Yii::$app->session->getFlash("remindMessage")): ?>
            <div class="success"><?=$message;?></div>
        <?php else: ?>
            <?php if($error = \Yii::$app->session->getFlash("error")): ?>
                <div class="error"><?=$error;?></div>
            <?php endif ;?>

            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($remindForm, "email", ["options" => ["class" => "fLine"], "template" => "{input}"])->textInput(["class" => "inp", "placeholder" => "Введите ваш E-mail"]); ?>
            <div class="fLine"><input class="signinBtn inpBtn" value="Восстановить пароль" type="submit"></div>
            <?php ActiveForm::end(); ?>
        <?php endif; ?>
    </div>
</div>