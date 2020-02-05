<?php
/* @var $this \yii\web\View */
/* @var $model \app\models\PhoneRequest */

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$title = "iOS приложение";
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

        <h2>Уже готово приложение для iOS, и в ближайшее время оно будет доступно в Apple Store</h2>

        <p style="text-align: center; margin-bottom: 5px;"><img src="/img/ios/logo.jpg"></p>

        <ul class="images">
            <li><img src="/img/ios/1.jpg"></li>
            <li><img src="/img/ios/2.jpg"></li>
            <li><img src="/img/ios/3.jpg"></li>
            <li><img src="/img/ios/4.jpg"></li>
        </ul>

    </div>
</div>