<?php

/* @var $this \yii\web\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */

use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use app\models\Call;

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'organization.number',
        'phone',
//        'id',
//        'tm',
//        'cuid',
        'duration',
//        'status',
        'organization.number',
        'organization.name',
        'organization.inn',
        'organization.maximum_sum',
        'organization.region',
        [
            'header' => 'Номера телефонов',
            'value' => function (Call $model) {
                return implode(', ', array_filter(ArrayHelper::getColumn($model->organization->phones, 'phone2')));
            }
        ],
    ]
]);
