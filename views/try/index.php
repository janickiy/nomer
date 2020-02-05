<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

/* @var $this \yii\web\View */

/* @var $user \app\models\User */
$user = \Yii::$app->getUser()->getIdentity();
?>

<div class="search">
    <div class="clfix">
        <h2>Попробовать бесплатно</h2>

        <?php if($user->is_test): ?>
            <p>К сожалению вы уже брали тестовый доступ :(</p><br />
        <?php else: ?>
            <p>На указанный ниже номер телефона придет смс с кодом подтверждения.</p><br />

            <?=Html::beginForm(["try/index"]);?>

            <?= MaskedInput::widget([
                'name' => 'phone',
                'mask' => '+7 ( 999 ) 999 - 99 - 99',
                'options' => [
                    'type' => 'tel',
                    'class' => 'searchPhone searchPhoneMain',
                    'placeholder' => '+7 ( ___ ) ___ - __ - __'
                ]
            ]); ?>
            <input type="submit" class="searchBtn inpBtn" value="Попробовать">
            <?=Html::endForm();?>
        <?php endif; ?>
    </div>
</div>
