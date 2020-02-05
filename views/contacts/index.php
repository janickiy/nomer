<?php

/* @var $this \yii\web\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $searchModel \app\models\search\UserContactSearch */
/* @var $pageSize string */

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\UserContact;

$this->title = 'Контакты';

?>

<div class="cont clfix" style="margin-top: 30px">
    <?= Html::dropDownList('pageSize', $pageSize, [
        '10' => 10,
        '20' => 20,
        '50' => 50,
        '10000' => 'Все'
    ]) ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'filterSelector' => 'select[name="pageSize"]',
        'columns' => [
            'phone',
            'name',
            [
                'attribute' => 'last_check',
                'value' => function(UserContact $model) {
                    if (!$model->last_check) return 'Проверка не производилась';
                    return $model->last_check;
                }
            ],
            [
                'format' => 'raw',
                'header' => 'Действия',
                'value' => function(UserContact $model) {
                    if (!preg_match('/(^7|^8)/', $model->phone)) return 'Скоро будет доступно';
                    return '<a href="' . Url::toRoute(['result/index', 'phone' => preg_replace('/^7/', '8', $model->phone)]) . '">Проверить</a>';
                }
            ]
        ]
    ]) ?>
</div>
