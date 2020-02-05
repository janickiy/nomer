<?php
use yii\helpers\Html;
use yii\widgets\MaskedInput;

/* @var $this \yii\web\View */
/* @var $user \app\models\User */

$user = \Yii::$app->getUser()->getIdentity();
?>

<div class="search">
    <div class="clfix">
        <h2>Подтверждение номера</h2>

        <p>Введите код из смс которая была отправлена на номер <?=$user->phone; ?></p><br />

        <?=Html::beginForm(["try/check"]);?>

        <?= MaskedInput::widget([
            'name' => 'code',
            'mask' => '9 9 9 9',
            'options' => [
                'type' => 'tel',
                'class' => 'searchPhone searchPhoneMain',
                'placeholder' => '_ _ _ _'
            ]
        ]); ?>
        <input type="submit" class="searchBtn inpBtn" value="Подтвердить">
        <?=Html::endForm();?>
    </div>
</div>
