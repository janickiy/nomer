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
} ?>

<?= $this->render("_form", ["phone" => $phone]); ?>


<div class="breadcrumbs">
        <ul class="breadcrumb">
            <?php if(\Yii::$app->devicedetect->isMobile() || \Yii::$app->devicedetect->isTablet()): ?>
                <li><?= Html::a("Главная", Url::home()); ?></li>
                <li><?= Html::a($seoPhone, Url::toRoute(["result/index", "phone" => $phone])); ?></li>
                <li><?= Html::a("avito", Url::toRoute(["result/avito", "phone" => $phone])); ?></li>

            <?php else: ?>
            <li><?= Html::a("Главная", Url::home()); ?></li>
            <li><?= Html::a("Результаты поиска по номеру " . $seoPhone, Url::toRoute(["result/index", "phone" => $phone])); ?></li>
            <li><?= Html::a("Объявления на avito", Url::toRoute(["result/avito", "phone" => $phone])); ?></li>
            <li><?= $item["title"]; ?></li>
            <?php endif; ?>
        </ul>
</div>

<div class="searchBox">
    <div class="cont clfix">

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
                                        <span class="title-info-title-text"><?= $item["title"]; ?></span>
                                    </h1>
                                </div>
                                <div class="title-info-metadata">
                                    <div class="title-info-metadata-item">
                                        <a href="<?= $item["url"]; ?>">№ <?= $item["avito_id"]; ?></a>,
                                        размещено <?= Yii::$app->formatter->asDatetime($item["time"], "d MMMM yyyy г."); ?>
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
                                                    <?= $item["name"]; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="seller-info-prop">
                                        <div class="seller-info-label">Адрес</div>
                                        <div class="seller-info-value">
                                            <?= join(", ", array_filter([$item["region"], $item["city"], $item["district"], $item["address"]])); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="item-price">
                                <div class="item-price-value-wrapper">

                                    <div class="price-value price-value_side-card" id="price-value">
                                        <span class="price-value-string"><?= Yii::$app->formatter->asCurrency($item["price"], "RUR"); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="item-view-main">
                            <?php if (count($images)): ?>
                                <div class="item-view-gallery">
                                    <div class="gallery">
                                        <div class="gallery-imgs-wrapper">
                                            <div class="gallery-imgs-container">
                                                <?php foreach ($images as $i => $img):
                                                    if(preg_match("/avito/", $img)) {
                                                        $img = "http://".$img;
                                                    } else {
                                                        $img = Url::toRoute(["site/image", "uuid" => $img]);
                                                    }

                                                ?>
                                                    <div class="gallery-img-wrapper">
                                                        <div class="gallery-img-frame">
                                                            <span class="gallery-img-cover"
                                                                  style="background-image: url('//30.img.avito.st/640x480/2933159130.jpg')"></span>
                                                            <img src="<?= $img; ?>"
                                                                 alt="<?= $item["title"]; ?> — фотография №<?= ++$i; ?>">
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                            <!--
                                            <div class="gallery-navigation gallery-navigation_prev"><span
                                                        class="gallery-navigation-icon"></span></div>
                                            <div class="gallery-navigation gallery-navigation_next"><span
                                                        class="gallery-navigation-icon"></span></div>
                                            -->
                                        </div>
                                        <div class="gallery-list-wrapper">
                                            <ul class="gallery-list">
                                                <?php foreach ($images as $i => $img): $img = Url::toRoute(["site/image", "uuid" => $img]); ?>
                                                    <li class="gallery-list-item" data-img="<?=$img;?>">
                                                        <span class="gallery-list-item-link"
                                                              title="<?= $item["title"]; ?> — фотография №<?= ++$i; ?>"
                                                              style="background-image: url(<?= $img; ?>);"></span>
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
                                        <?php foreach ($item["params"] as $param): ?>
                                            <li class="item-params-list-item">
                                                <span class="item-params-label"><?= $param["name"]; ?>
                                                    : </span> <?= $param["value"]; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                            <div class="item-view-block">
                                <div class="item-description">
                                    <div class="item-description-text">
                                        <p><?= $item["description"]; ?></p></div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="item-view-right">
                        <div class="item-view-price">

                            <div class="item-price">
                                <div class="item-price-value-wrapper">

                                    <div class="price-value price-value_side-card" id="price-value">
                                        <span class="price-value-string"><?= Yii::$app->formatter->asCurrency($item["price"], "RUR"); ?></span>
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
                                                    <?= $item["name"]; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="seller-info-prop">
                                        <div class="seller-info-label">Адрес</div>
                                        <div class="seller-info-value">
                                            <?= join(", ", array_filter([$item["region"], $item["city"], $item["district"], $item["address"]])); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if(false):?><script><?php endif; ?>
<?php ob_start(); ?>
    $('.gallery-list-item').css('cursor', 'pointer');
    $('.gallery-list-item').on('click', function() {
        var img = $(this).data('img');
        $('.gallery-img-frame img').attr('src', img);
    });
<?php $js = ob_get_contents(); ob_end_clean(); $this->registerJs($js); ?>
