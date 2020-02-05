<?php
/* @var $result array */
/* @var $this \yii\web\View */
/* @var $phone String */
/* @var $id int */

use app\components\PhoneHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$seoPhone = preg_replace("/^7(\d\d\d)(\d\d\d)(\d\d)(\d\d)$/", "8 ($1) $2-$3-$4", $phone);

$this->title = 'Объявления на avito по номеру телефона: ' . join(", ", PhoneHelper::getFormats($phone));

$item = null;
?>
<?php foreach ($result as $item) {
    if ($item["Id"] == $id) break;
}?>


<div class="row">
    <div class="col-md-offset-3 col-md-6 col-xs-12">
        <h1 class="header__title">Объявления на avito по номеру телефона:<br><?= $seoPhone; ?></h1>
    </div>
</div>

<div class="row">
    <div class="col-md-12 col-xs-12">
        <ul class="breadcrumb">
            <li><?=Html::a(\Yii::$app->name, Url::home());?></li>
            <li><?=Html::a("Результаты поиска по номеру ".$seoPhone, Url::toRoute(["result/index", "phone" => $phone]));?></li>
            <li><?=Html::a("Объявления на avito", Url::toRoute(["result/avito", "phone" => $phone]));?></li>
            <li><?=$item["title"];?></li>
        </ul>
    </div>
</div>

<br>

<div class="result-avito">
        <?php
        $item["images"] = preg_replace('/http:/', '', $item["images"]);
        $images = array_filter(preg_split('/,/', $item["images"]));
        ?>
        <div class="item-view">
            <div class="item-view-content">
                <div class="item-view-left">
                    <div class="item-view-title-info js-item-view-title-info">
                        <div class="title-info title-info_mode-with-favorite">
                            <div class="title-info-main">
                                <h1 class="title-info-title">
                                    <span class="title-info-title-text"><?=$item["title"];?></span>
                                </h1>
                            </div>
                            <div class="title-info-metadata">
                                <div class="title-info-metadata-item">
                                    № <?=$item["avito_id"];?>, размещено <?=Yii::$app->formatter->asDatetime($item["time"], "d MMMM yyyy г.");?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="item-view-main">
                        <?php if(count($images)): ?>
                        <div class="item-view-gallery">
                            <div class="gallery">
                                <div class="gallery-imgs-wrapper">
                                    <div class="gallery-imgs-container">
                                        <?php foreach($images as $i => $img): ?>
                                        <div class="gallery-img-wrapper">
                                            <div class="gallery-img-frame">
                                                <span class="gallery-img-cover" style="background-image: url('//30.img.avito.st/640x480/2933159130.jpg')"></span>
                                                <img src="<?=$img;?>" alt="<?=$item["title"];?> — фотография №<?=++$i;?>">
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="gallery-navigation gallery-navigation_prev"><span class="gallery-navigation-icon"></span></div>
                                    <div class="gallery-navigation gallery-navigation_next"><span class="gallery-navigation-icon"></span></div>
                                </div>
                                <div class="gallery-list-wrapper">
                                    <ul class="gallery-list">
                                        <?php foreach($images as $i => $img): ?>
                                        <li class="gallery-list-item">
                                            <span class="gallery-list-item-link" title="Д<?=$item["title"];?> — фотография №<?=++$i;?>" style="background-image: url(<?=$img;?>);"></span>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="item-view-block">
                            <div class="item-params">
                                <ul class="item-params-list">
                                <?php foreach($item["params"] as $param): ?>
                                    <li class="item-params-list-item">
                                        <span class="item-params-label"><?=$param["name"];?>: </span> <?=$param["value"];?>
                                    </li>
                                <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                        <div class="item-view-block">
                            <div class="item-description">
                                <div class="item-description-text">
                                    <p><?=$item["description"];?></p></div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="item-view-right">
                    <div class="item-view-price">

                        <div class="item-price">
                            <div class="item-price-value-wrapper">

                                <div class="price-value price-value_side-card" id="price-value">
                                    <span class="price-value-string"><?=Yii::$app->formatter->asCurrency($item["price"], "RUR");?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="item-view-contacts">
                        <div class="item-view-seller-info">
                            <div class="seller-info">
                                <div
                                    class="seller-info-prop seller-info-prop_layout-two-col">
                                    <div class="seller-info-col">
                                        <div class="seller-info-label">Продавец</div>
                                        <div class="seller-info-value">
                                            <div class="seller-info-name">
                                                <?=$item["name"];?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="seller-info-prop">
                                    <div class="seller-info-label">Адрес</div>
                                    <div class="seller-info-value">
                                        <?=join(", ", array_filter([$item["region"], $item["city"], $item["district"], $item["address"]]));?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
