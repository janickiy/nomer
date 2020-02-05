<?php

/* @var $this \yii\web\View */

/* @var $phone string */

use app\components\PhoneHelper;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

$seoPhone = preg_replace("/^7(\d\d\d)(\d\d\d)(\d\d)(\d\d)$/", "8 ($1) $2-$3-$4", $phone);

$this->title = 'Результаты поиска в скористе по номеру телефона: ' . join(", ", PhoneHelper::getFormats($phone));

?>

<?= $this->render("_form", ["phone" => $phone]); ?>

<div class="searchBox">
    <div class="cont clfix">

        <div class="row">
            <div class="col-md-12 col-xs-12">
                <ul class="breadcrumb">
                    <li><?= Html::a(\Yii::$app->name, Url::home()); ?></li>
                    <li><?= Html::a("Результаты поиска по номеру " . $seoPhone, Url::toRoute(["result/index", "phone" => $phone])); ?></li>
                    <li>Результаты поиска в Скориста</li>
                </ul>
            </div>
        </div>

        <br>
        <div class="result-scorista">
            <?php
            $result = Json::decode($result);
            $items = ArrayHelper::getValue($result, "DetailItems");
            ?>
            <?php foreach ($items as $parts): ?>
                <div class="scorista_item">
                <?php foreach ($parts as $p): ?>
                    <?php if(preg_match("/паспорт/iu", ArrayHelper::getValue($p, "title"))): continue; endif; ?>
                    <?php if(trim(ArrayHelper::getValue($p, "value")) === ""): continue; endif; ?>
                    <p>
                        <b><?= ArrayHelper::getValue($p, "title"); ?></b>: <?= ArrayHelper::getValue($p, "value"); ?>
                    </p>
                <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</div>