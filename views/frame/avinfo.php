<?php
/* @var $result array */
/* @var $this \yii\web\View */
/* @var $phone String */
/* @var $urls array */

use app\components\PhoneHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$seoPhone = preg_replace("/^7(\d\d\d)(\d\d\d)(\d\d)(\d\d)$/", "8 ($1) $2-$3-$4", $phone);

$this->title = 'Результаты поиска на auto.ru по номеру телефона: '.join(", ", PhoneHelper::getFormats($phone));

?>

<div class="row">
    <div class="col-md-offset-3 col-md-6 col-xs-12">
        <h1 class="header__title">Результаты поиска на auto.ru по номеру телефона:<br><?=$seoPhone;?></h1>
    </div>
</div>

<div class="row">
    <div class="col-md-12 col-xs-12">
        <ul class="breadcrumb">
            <li><?=Html::a(\Yii::$app->name, Url::home());?></li>
            <li><?=Html::a("Результаты поиска по номеру ".$seoPhone, Url::toRoute(["result/index", "phone" => $phone]));?></li>
            <li>Результаты поиска на auto.ru</li>
        </ul>
    </div>
</div>

<br>
<div class="result-avinfo">
    <?php foreach($result as $r): if(!isset($r["date"])): continue; endif; ?>
        <div class="item">
            <h3><?=isset($r["date"])?$r["date"].", ":"";?><?=$r["site"];?> - <?=$r["text"];?></h3>
            <?php if(count($r["photos"])): ?>
                <?php foreach ($r["photos"] as $photo): $photo =  Url::toRoute(["site/image", "uuid" => $photo]); ?>
                    <a href="<?=$photo;?>" data-fancybox data-width="700" data-caption="<?=isset($r["date"])?$r["date"].", ":"";?><?=$r["site"];?> - <?=$r["text"];?>">
                        <img src="<?=$photo;?>" alt="" width="100"/>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

