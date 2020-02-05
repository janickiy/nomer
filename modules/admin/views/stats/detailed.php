<?php

/* @var $this \yii\web\View */
/* @var $type integer */
/* @var $date string */
/* @var $filter string */
/* @var $dataProvider \yii\data\ActiveDataProvider */

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\ResultCache;

$this->title = 'Статистика по ' . ResultCache::getTypeName($type) . ' за ' . $date;

echo Html::dropDownList('filter', $filter, [
    'all' => 'Все',
    'found' => 'Найден',
    'not_found' => 'Не найден'
]);

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterSelector' => 'select[name="filter"]',
    'columns' => [
        [
            'format' => 'raw',
            'header' => 'Номер телефона',
            'value' => function ($row) {
                return Html::a($row['phone'], Url::to('/' . preg_replace('/^7/', '8', $row['phone'])));
            }
        ],
        [
            'header' => 'Найден',
            'value' => function ($row) {
                return $row['success'] ? 'Да' : 'Нет';
            }
        ]
    ]
]);
