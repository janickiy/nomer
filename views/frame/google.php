<?php
/* @var $result array */
/* @var $this \yii\web\View */
/* @var $phone String */
/* @var $urls array */

use app\components\PhoneHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$seoPhone = preg_replace("/^7(\d\d\d)(\d\d\d)(\d\d)(\d\d)$/", "8 ($1) $2-$3-$4", $phone);

$this->title = 'Результаты поиска в google по номеру телефона: '.join(", ", PhoneHelper::getFormats($phone));

?>

<div class="row">
    <div class="col-md-offset-3 col-md-6 col-xs-12">
        <h1 class="header__title">Результаты поиска в google по номеру телефона:<br><?=$seoPhone;?></h1>
    </div>
</div>

<div class="row">
    <div class="col-md-12 col-xs-12">
        <ul class="breadcrumb">
            <li><?=Html::a(\Yii::$app->name, Url::home());?></li>
            <li><?=Html::a("Результаты поиска по номеру ".$seoPhone, Url::toRoute(["result/index", "phone" => $phone]));?></li>
            <li>Результаты поиска в google</li>
        </ul>
    </div>
</div>

<br>

<div class="result-google">
    <?php foreach($result as list($url, $title, $cache, $desc)): $shortUrl = urldecode(preg_replace('/(http|https)\:\/\/(.+?)\/(.*)/', '$2', $url)); ?>
        <?php if(array_key_exists($shortUrl, $urls) && $urls[$shortUrl] == 1) { continue; }?>
        <div>
            <h3 class="r">
                <?php if(array_key_exists($shortUrl, $urls) && $urls[$shortUrl] == 2): ?>
                    [Доверенный URL]
                <?php endif;?>
                <a href="<?=$url;?>" target="_blank"><?=$title;?></a>
            </h3>
            <div class="s">
                <cite>[<a href="<?=$cache;?>" target="_blank">Сохраненная копия</a>] <?=urldecode(preg_replace('/(http|https)\:\/\//', '', $url));?></cite>
                <span class="st"><?=$desc;?></span>
            </div>
            <?php if(!\Yii::$app->getUser()->isGuest && \Yii::$app->getUser()->getIdentity()->is_admin && !array_key_exists($shortUrl, $urls)): ?>
                <div class="buttons">
                    <input type="button" value="Забанить домен" data-url="<?=$shortUrl;?>" data-action="url" data-type="1">
                    <input type="button" value="Доверенный домен" data-url="<?=$shortUrl;?>" data-action="url" data-type="2">
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

