<?php

/* @var $this \yii\web\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */

use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;
use app\models\User;

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        [
            'format' => 'raw',
            'attribute' => 'email',
            'value' => function(User $model) {
                return Html::a($model->email, Url::toRoute(['users/view', 'id' => $model->id]));
            }
        ],
        [
            'attribute' => 'ban',
            'value' => function(User $model) {
                return User::getBanStatusText($model->ban);
            }
        ]
    ]
]);
