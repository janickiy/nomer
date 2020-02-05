<?php
/* @var $result array */
/* @var $this \yii\web\View */
/* @var $phone String */
use app\components\PhoneHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$seoPhone = preg_replace("/^7(\d\d\d)(\d\d\d)(\d\d)(\d\d)$/", "8 ($1) $2-$3-$4", $phone);

$this->title = 'Сырые данные из "В контакте" за 2012 год по номеру телефона: '.join(", ", PhoneHelper::getFormats($phone));
?>

<div class="row">
    <div class="col-md-offset-3 col-md-6 col-xs-12">
        <h1 class="header__title">Сырые данные из "В контакте" за 2012 год по номеру телефона:<br><?=$seoPhone;?></h1>
    </div>
</div>

<div class="row">
    <div class="col-md-12 col-xs-12">
        <ul class="breadcrumb">
            <li><?=Html::a(\Yii::$app->name, Url::home());?></li>
            <li><?=Html::a("Результаты поиска по номеру ".$seoPhone, Url::toRoute(["result/index", "phone" => $phone]));?></li>
            <li>Сырые данные из "В контакте" за 2012 год</li>
        </ul>
    </div>
</div>

<br>

<div class="result-vk">
    <?php foreach ($result as $item): ?>
        <pre><?php print_r(mb_convert_encoding($item["raw"], 'CP1251', 'UTF-8')); ?></pre>
    <?php endforeach; ?>
</div>