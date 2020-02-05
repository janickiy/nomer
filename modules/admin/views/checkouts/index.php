<?php
/**
 * Created by PhpStorm.
 * User: firedemon
 * Date: 8/3/17
 * Time: 7:00 PM
 */

use app\models\User;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = 'Запросы на выплаты';

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        'tm_create',
        'user_id',
        'sum',
        [
            'header' => 'Сумма',
            'content' => function($model) {
                return \Yii::$app->formatter->asCurrency($model->sum, "RUB");
            }
        ],
        [
            'header' => 'Пользователь',
            'content' => function($model) {
                $user = User::find()->where(["id" => $model->user_id])->one();
                return Html::a($user->email, ["users/view", "id" => $user->id]);
            }
        ],
        [
            'class' => ActionColumn::className(),
            'template' => '{done}',
            'buttons' => [
                'done' => function ($url, $model, $key) {
                    return Html::a('Выплачено', ['done', 'id' => $model->id], ['class' => 'btn btn-primary']);
                }
            ]
        ],
    ]
]);

