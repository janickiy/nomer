<?php
/* @var $this \yii\web\View */
/* @var $phone int */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\MaskedInput;

$this->title = \Yii::$app->name.' - подтверждение исключения номераиз базы';
?>

<div class="breadcrumbs">
    <ul class="breadcrumb">
        <?php if(\Yii::$app->devicedetect->isMobile() || \Yii::$app->devicedetect->isTablet()): ?>
            <li><?= Html::a('Главная', Url::home()) ?></li>
            <li>Исключение номера из базы</li>
        <?php else: ?>
            <li><?= Html::a('Главная', Url::home()) ?></li>
            <li>Исключение номера из базы</li>
        <?php endif; ?>
    </ul>
</div>

<div class="page-content">
    <div class="cont clfix">
        <h1>Подтверждение исключения номера</h1>
        <h2><?=preg_replace("/7(\d\d\d)(\d\d\d)(\d\d)(\d\d)/", "+7 ($1) $2-$3-$4", $phone);?></h2>

        <?php if(Yii::$app->session->get('smsBlockPhone', false) === true): ?>
            <h2>На указанный номер поступит смс сообщение!</h2>
        <?php else: ?>
            <h2>На указанный номер поступит телефонный звонок!</h2>
        <?php endif ;?>

        <?=Html::beginForm(["block/confirm"], 'post', ["style" => 'text-align: center;']);?>
            <?= MaskedInput::widget([
                'name' => 'code',
                'mask' => '9 9 9',
                'options' => [
                    'class' => 'blockCode',
                    'placeholder' => '_ _ _'
                ]
            ]); ?>
            <input class="button" value="Исключить" type="submit" style="margin: 20px auto">
        <?=Html::endForm();?>

        <?php if(Yii::$app->session->get('recallBlockPhone', false) === false): ?>
            <a href="<?=Url::toRoute(["block/recall"]);?>" class="button">Запросить ещё один звонок</a>
        <?php elseif(Yii::$app->session->get('smsBlockPhone', false) === false): ?>
            <a href="<?=Url::toRoute(["block/sms"]);?>" class="button">Запросить смс с кодом</a>
        <?php endif; ?>
    </div>
</div>