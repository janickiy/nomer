<?php

/* @var $result array */
/* @var $this \yii\web\View */
/* @var $phone String */

use app\components\PhoneHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$seoPhone = preg_replace("/^7(\d\d\d)(\d\d\d)(\d\d)(\d\d)$/", "8 ($1) $2-$3-$4", $phone);

$this->title = 'Объявления на avito по номеру телефона: '.join(", ", PhoneHelper::getFormats($phone));

krsort($result);

?>

<?= $this->render('_form', compact('phone')) ?>

<div class="breadcrumbs">
        <ul class="breadcrumb">
            <?php if(\Yii::$app->devicedetect->isMobile() || \Yii::$app->devicedetect->isTablet()): ?>
                <li><?= Html::a('Главная', Url::home()) ?></li>
                <li><?= Html::a($seoPhone, Url::toRoute(['result/index', 'phone' => $phone])) ?></li>
                <li>avito</li>
            <?php else: ?>
            <li><?= Html::a('Главная', Url::home()) ?></li>
            <li><?= Html::a("Результаты поиска по номеру ".$seoPhone, Url::toRoute(['result/index', 'phone' => $phone])) ?></li>
            <li>Объявления на avito</li>
            <?php endif; ?>
        </ul>
</div>

<div class="searchBox">
    <div class="cont clfix">
<div class="result-avito">
    <?php foreach ($result as $item): ?>
        <?php
        $item["images"] = preg_replace('/http:/', '', $item["images"]);
        $images = preg_split('/,/', $item["images"]);
        ?>
        <div class="result-avito-item">
            <div class="img">
                <img src="<?= $images[0]?(preg_match("/avito/", $images[0])?"http://".$images[0]:Url::toRoute(['site/image', 'uuid' => $images[0]])):"/img/nophoto.png" ?>">
            </div>
            <div class="descr">
                <h3><a href="<?=Url::toRoute(["result/avito", "phone" => $phone, "id" => $item["Id"]]);?>"><?=$item["title"];?></a></h3>
                <span><?=Yii::$app->formatter->asCurrency($item["price"], "RUR");?></span>
                <p><?=$item["description"];?></p>
                <span class="date"><?=Yii::$app->formatter->asDatetime($item["time"], "d MMMM yyyy г.");?></span>
            </div>
        </div>
    <?php endforeach; ?>
</div>
</div>
    </div>