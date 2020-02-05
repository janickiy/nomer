<?php
/* @var $this \yii\web\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $todayUsers integer */
/* @var $totalUsers integer */

use yii\grid\GridView;
use \yii\helpers\Html;
use \yii\helpers\Url;

$this->title = "Реферальная программа";

/* @var $user \app\models\User */
$user = \Yii::$app->getUser()->getIdentity();

$checkoutSum = $user->ref_balance;
?>

<div class="breadcrumbs">
    <ul class="breadcrumb">
        <?php if(\Yii::$app->devicedetect->isMobile() || \Yii::$app->devicedetect->isTablet()): ?>
            <li><?= Html::a('Главная', Url::home()) ?></li>
            <li><?=$this->title;?></li>
        <?php else: ?>
            <li><?= Html::a('Главная', Url::home()) ?></li>
            <li><?=$this->title;?></li>
        <?php endif; ?>
    </ul>
</div>

<div class="page-content">
    <div class="cont clfix">
        <h1><?=$this->title;?></h1>
        <h2>30% с дохода от привлеченного юзера</h2>

        <p>Ваша ссылка для привлечения рефералов: <b style="font-weight: bold"><?=Url::toRoute(["referrals/new", "id" => \Yii::$app->getUser()->getId()], 'https');?></b></p>
        <br>
        <p>Зарегистрированных по вашей ссылке сегодня - <?=$todayUsers;?></p>
        <p>Зарегистрированных по вашей ссылке всего - <?=$totalUsers;?></p>
        <p>Денег заработано вами всего: <?=$sum;?></p>
        <p>Доступно для вывода: <b><?=Yii::$app->formatter->asCurrency($checkoutSum, 'RUB');?></b></p>

        <?=Html::beginForm(["referrals/checkout"]); ?>
        <?=Html::textInput("wallet", "", ["placeholder" => "Ваш кошелек", "class" => "textInput"]); ?>
        <?php if($checkoutSum >= 5000): ?>
            <?=Html::submitButton("Запросить вывод", ["class" => "button", "style" => "display: inline;"]);?>
        <?php else: ?>
            <?=Html::button("Запросить вывод", ["class" => "button", "style" => "display: inline;", "disabled" => "disabled"]);?>
        <?php endif; ?>
        <?=Html::endForm();?>

        <br>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                'tm_create',
                'email',
                'checks'
            ]
        ]) ?>
    </div>
</div>