<?php

/* @var $this \yii\web\View */
/* @var $items int */
/* @var $phone string */

use yii\helpers\Url;

if (!$items) {
    echo 'Ничего не найдено';
    return;
}

$lines = explode("\n", $items);

echo implode("\n", array_splice($lines, 0, 10));

if (count($lines) > 10) {
    echo '<div class="allRes"><a href="' . Url::toRoute(["result/scorista", "phone" => $phone]) . '">Подробнее</a></div>';
}

