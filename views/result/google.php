<?php
/* @var $result array */
/* @var $this \yii\web\View */
/* @var $phone String */
/* @var $urls array */

use app\components\PhoneHelper;
use app\models\UrlFilter;
use yii\helpers\Html;
use yii\helpers\Url;

$seoPhone = preg_replace("/^7(\d\d\d)(\d\d\d)(\d\d)(\d\d)$/", "8 ($1) $2-$3-$4", $phone);

$this->title = 'Результаты поиска в google по номеру телефона: '.join(", ", PhoneHelper::getFormats($phone));

if(!isset($urls) || !is_array($urls)) {
    $urls = [];
}
$queries = [];
if(isset($result["queries"])) {
    $queries = $result["queries"];
}
if(isset($result["items"])) $result = $result["items"];
?>

<?=$this->render("_form", ["phone" => $phone]); ?>

<div class="breadcrumbs">
        <ul class="breadcrumb">
            <?php if(\Yii::$app->devicedetect->isMobile() || \Yii::$app->devicedetect->isTablet()): ?>
                <li><?=Html::a("Главная", Url::home());?></li>
                <li><?=Html::a($seoPhone, Url::toRoute(["result/index", "phone" => $phone]));?></li>
                <li>google</li>
            <?php else: ?>
            <li><?=Html::a("Главная", Url::home());?></li>
            <li><?=Html::a("Результаты поиска по номеру ".$seoPhone, Url::toRoute(["result/index", "phone" => $phone]));?></li>
            <li>Результаты поиска в google</li>
            <?php endif; ?>
        </ul>
</div>


<div class="searchBox">
    <div class="cont clfix">

<div class="result-google">
    <?php if(\Yii::$app->getUser()->getIdentity()->is_vip && count($queries)): ?>
        <ul>
        <?php foreach ($queries as $query => $count): ?>
            <li><?=$query;?> - <?=$count;?></li>
        <?php endforeach;?>
        </ul>
    <?php endif;?>

    <?php foreach($result as $row):
        if(count($row) == 4) {
            list($url, $title, $cache, $desc) = $row;
        } else {
            $cache = null;
            list($url, $title, $desc) = $row;
        }

        $shortUrl = urldecode(preg_replace('/(http|https)\:\/\/(.+?)\/(.*)/', '$2', $url)); ?>
        <?php if(array_key_exists($shortUrl, $urls) && $urls[$shortUrl] == 1) { continue; }?>
        <div>
            <h3 class="r">
                <?php if(array_key_exists($shortUrl, $urls) && $urls[$shortUrl] == 2 && \Yii::$app->getUser()->getIdentity()->is_admin): ?>
                    [Доверенный URL]
                <?php endif;?>
                <a href="<?=$url;?>" target="_blank"><?=$title;?></a>
            </h3>
            <div class="s">
                <cite>
                    <?php if($cache): ?>
                    [<a href="<?=$cache;?>" target="_blank">Сохраненная копия</a>]
                    <?php endif; ?>
                    <?=urldecode(preg_replace('/(http|https)\:\/\//', '', $url));?></cite>
                <span class="st"><?=$desc;?></span>
            </div>
            <?php if(!\Yii::$app->getUser()->isGuest && \Yii::$app->getUser()->getIdentity()->is_admin && !array_key_exists($shortUrl, $urls)): ?>
                <div class="buttons">
                    <input type="button" value="Забанить домен" data-url="<?=$shortUrl;?>" data-action="url" data-type="<?= UrlFilter::TYPE_BANNED ?>">
                    <input type="button" value="Доверенный домен" data-url="<?=$shortUrl;?>" data-action="url" data-type="<?= UrlFilter::TYPE_TRUSTED ?>">
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
</div>
</div>