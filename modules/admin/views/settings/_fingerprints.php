<?php

/* @var $this \yii\web\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */

use yii\grid\GridView;
use app\models\UserFingerprint;
use \app\models\User;
use \yii\helpers\ArrayHelper;

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'hash',
        [
            'attribute' => 'user_ids',
            'header' => 'Пользователи',
            'value' => function ($data) {
                $userIds = trim(ArrayHelper::getValue($data, 'user_ids'), '{}');
                $userIds = preg_split('/,/', $userIds);
                $users = User::find()->where(['id' => $userIds])->all();
                return join(', ', ArrayHelper::getColumn($users, 'email'));
            }
        ],
        [
            'attribute' => 'ips',
            'header' => 'IP адреса',
            'value' => function ($data) {
                $ips = trim(ArrayHelper::getValue($data, 'ips'), '{}');
                $ips = preg_split('/,/', $ips);
                return join(', ', $ips);
            }
        ],
    ]
]);
