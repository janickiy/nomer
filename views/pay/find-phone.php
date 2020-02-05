<?php
/* @var $this \yii\web\View */
/* @var $request \app\models\PhoneRequest */
/* @var $id integer */
/* @var $dataType string */
/* @var $data string|array */

use \app\components\ConfigHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$sum = 1000;

$order = new \app\models\WebmoneyOrder();
$order->sum = $sum;
$order->site_id = ConfigHelper::getInstance()->getSiteId();
$order->user_id = -$id;
$order->save();

$site = \app\models\Site::find()->where(["name" => $_SERVER["HTTP_HOST"]])->one();

$this->title = "Подтверждение";
?>

<div class="breadcrumbs">
    <ul class="breadcrumb">
        <?php if(\Yii::$app->devicedetect->isMobile() || \Yii::$app->devicedetect->isTablet()): ?>
            <li><?= Html::a('Главная', Url::home()) ?></li>
            <li><?= Html::a("Оплата поиска номера телефона", Url::toRoute(['pay/index'])) ?></li>
        <?php else: ?>
            <li><?= Html::a('Главная', Url::home()) ?></li>
            <li><?= Html::a("Оплата поиска номера телефона", Url::toRoute(['pay/index'])) ?></li>
            <li><?=$this->title;?></li>
        <?php endif; ?>
    </ul>
</div>

<div class="page-content">
    <div class="cont clfix">


        <?php if($dataType == "email" || $dataType == "instagram"): ?>
            <h2><?=$data;?></h2>
        <?php endif; ?>
        <?php if($dataType == "vk"): ?>
            <h2><?=$data["first_name"];?> <?=$data["last_name"];?><br><?=Html::img($data["photo_max_orig"], ["width" => 400]);?></h2>
        <?php endif; ?>
        <?php if($dataType == "fb"): ?>
            <h2><?=$data["first_name"];?> <?=$data["last_name"];?><br><?=Html::img($data["photo"], ["width" => 400]);?></h2>
        <?php endif; ?>

        <h2>Мы можем найти для вас телефон данного человека, вам необходимо заплатить 1000р.</h2>

        <p style="text-align: center;"><a href="<?=Url::toRoute(["pay/find-phone-confirm", "id" => $id]);?>" class="button" style="width: 300px; display: inline-block">Оплатить</a></p>

    </div>
</div>

