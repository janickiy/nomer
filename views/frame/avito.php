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

<div class="row">
    <div class="col-md-offset-3 col-md-6 col-xs-12">
        <h1 class="header__title">Объявления на avito по номеру телефона:<br><?=$seoPhone;?></h1>
    </div>
</div>

<div class="row">
    <div class="col-md-12 col-xs-12">
        <ul class="breadcrumb">
            <li><?=Html::a(\Yii::$app->name, Url::home());?></li>
            <li><?=Html::a("Результаты поиска по номеру ".$seoPhone, Url::toRoute(["result/index", "phone" => $phone]));?></li>
            <li>Объявления на avito</li>
        </ul>
    </div>
</div>

<br>

<div class="result-avito">
    <?php foreach ($result as $item): ?>
        <?php
        $item["images"] = preg_replace('/http:/', '', $item["images"]);
        $images = preg_split('/,/', $item["images"]);
        ?>
        <div class="result-avito-item">
            <div class="img">
                <img src="<?=$images[0];?>">
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
