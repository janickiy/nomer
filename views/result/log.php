<?php

/* @var $log array */
/* @var $this \yii\web\View */
/* @var $phone String */

use app\components\PhoneHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$seoPhone = preg_replace("/^7(\d\d\d)(\d\d\d)(\d\d)(\d\d)$/", "8 ($1) $2-$3-$4", $phone);

$this->title = 'История поисков по номеру телефона: '.join(", ", PhoneHelper::getFormats($phone));
?>

<?= $this->render('_form', compact('phone')) ?>

<div class="breadcrumbs">
    <ul class="breadcrumb">
        <?php if(\Yii::$app->devicedetect->isMobile() || \Yii::$app->devicedetect->isTablet()): ?>
            <li><?= Html::a('Главная', Url::home()) ?></li>
            <li><?= Html::a($seoPhone, Url::toRoute(['result/index', 'phone' => $phone])) ?></li>
            <li>История</li>
        <?php else: ?>
        <li><?= Html::a('Главная', Url::home()) ?></li>
        <li><?= Html::a("Результаты поиска по номеру ".$seoPhone, Url::toRoute(['result/index', 'phone' => $phone])) ?></li>
        <li>История поисков</li>
        <?php endif; ?>
    </ul>
</div>

<div class="searchBox">
    <div class="cont clfix">
        <ul>
            <?php foreach($log as $l): ?>
                <li>
                    <?php if(preg_match("/TelegramBot/", $l["ua"])): ?>
                        Antiparkon:
                    <?php endif; ?>
                    <?=$l["tm"];?>;
                    <?=$l["ip"];?>;
                    <?php
                    switch($l["source_id"]) {
                        case \app\models\SearchRequest::SOURCE_WEB: echo "Desktop"; break;
                        case \app\models\SearchRequest::SOURCE_MOBILE: echo "Mobile"; break;
                        case \app\models\SearchRequest::SOURCE_IOS: echo "IOS"; break;
                        case \app\models\SearchRequest::SOURCE_ANDROID: echo "Android"; break;
                        default: echo "Не определн";
                    }
                    ?>;
                    <?php if($l["user_id"]): ?>
                        <?=$l["user"]["email"];?>
                    <?php else: ?>
                        Аноним
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>