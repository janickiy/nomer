<?php
/* @var $this \yii\web\View */
/* @var $model \app\models\PhoneRequest */

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$title = "Поиск номера телефона";
?>
<div class="breadcrumbs">
    <ul class="breadcrumb">
        <?php if(\Yii::$app->devicedetect->isMobile() || \Yii::$app->devicedetect->isTablet()): ?>
            <li><?= Html::a('Главная', Url::home()) ?></li>
            <li><?=$title;?></li>
        <?php else: ?>
            <li><?= Html::a('Главная', Url::home()) ?></li>
            <li><?=$title;?></li>
        <?php endif; ?>
    </ul>
</div>

<div class="page-content">
    <div class="cont clfix">
        <h1><?=$title;?></h1>

        <p>Мы можем предоставить вам номер телефона человека по его профилю фейсбук, вконтакте, инстаграмм, емейл или другому номеру телефона. Вам достаточно указать все данные которые вы знаете в поле ниже и в течении нескольких часов оператор пришлёт вам его номер телефона или сделает возврат средств на указанный вами кошелек или номер телефона.</p>

        <p style="font-weight: bold; color: darkred">ВНИМАНИЕ!!! Мы не работаем со знаменитостями и известными личностями!!!</p>

        <?php if(\Yii::$app->getUser()->isGuest): ?>
            <h2 style="margin-top: 15px;">Что бы продолжить, Вам нужно авторизоваться!</h2>
            <a class="button" href="#signup">Войти / Зарегистрироваться</a>
        <?php else: ?>
            <br>
            <?php $form = ActiveForm::begin(['options' => ['class' => 'search', 'style' => 'padding: 2.51% 0 8.2%']]); ?>
            <?=$form->field($model, "data")->textInput(['class' => 'findPhone']);?>
            <?=Html::submitButton("Продолжить", ['class' => 'button', 'style' => 'display: inline; margin-top: 15px;']);?>
            <?php ActiveForm::end(); ?>
        <?php endif; ?>
    </div>
</div>
