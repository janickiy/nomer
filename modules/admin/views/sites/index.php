<?php

/* @var $this \yii\web\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */

use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = 'Список сайтов';

echo Html::tag('p', Html::a('Добавить сайт', ['sites/create'], ['class' => 'btn btn-primary']));

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        'name',
        'comment',
        'is_demo',
        [
            'header' => 'Платежи',
            'content' => function($model) use($payments) {
                return \Yii::$app->formatter->asCurrency(\yii\helpers\ArrayHelper::getValue($payments, [$model->id, "sum"], 0), "RUB");
            }
        ],
        [
            'header' => 'Поиски',
            'content' => function($model) use($seaches) {
                return \yii\helpers\ArrayHelper::getValue($seaches, [$model->id, "count"], 0);
            }
        ],
        [
            'class' => ActionColumn::className(),
            'template' => '{update} {delete}',
            'buttons' => [
                'set-demo' => function ($url, $model, $key) {
                    return $model->is_demo ? Html::a('Выключить demo', ['set-demo', 'id' => $model->id], ['class' => 'btn btn-danger']) : Html::a('Включить demo', ['set-demo', 'id' => $model->id], ['class' => 'btn btn-success']);
                }
            ]
        ],
    ]
]);
