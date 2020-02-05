<?php

use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;


$this->title = 'Ретаргетинг';

?>

<h3>Статистика</h3>

<table class="table table-striped table-bordered">
    <tr>
        <th></th>
        <th>Сегодня</th>
        <th>Вчера</th>
        <th>За последние 30 дней</th>
    </tr>
    <tr>
        <td>Кол-во отправленных писем:</td>
        <td><?= $todaySent ? $todaySent : 0 ?></td>
        <td><?= $todayRead ? $todayRead : 0 ?></td>
        <td><?= $todayClick ? $todayClick : 0 ?></td>
    </tr>
    <tr>
        <td>Кол-во прочитанных писем:</td>
        <td><?= $yesterdaySent ?></td>
        <td><?= $yesterdayRead ?></td>
        <td><?= $yesterdayClick ?></td>
    </tr>
    <tr>
        <td>Кол-во переходов по ссылке:</td>
        <td><?= $monthSent ?></td>
        <td><?= $monthRead ?></td>
        <td><?= $monthClick ?></td>
    </tr>
</table>