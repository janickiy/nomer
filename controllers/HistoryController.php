<?php

namespace app\controllers;

use app\models\SearchRequest;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;

class HistoryController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@']
                    ]
                ]
            ],
        ];
    }

    public function actionIndex()
    {
        /* @var $user \app\models\User */
        $user = Yii::$app->getUser()->getIdentity();

        $dataProvider = new ActiveDataProvider([
            'query' => SearchRequest::find()->where(['user_id' => $user->id])->with('results'),
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        return $this->render('index', compact('dataProvider'));
    }
}
