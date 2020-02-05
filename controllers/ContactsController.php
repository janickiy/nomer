<?php

namespace app\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use app\models\search\UserContactSearch;

class ContactsController extends Controller
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

    public function actionIndex($pageSize = 20)
    {
        $searchModel = new UserContactSearch();
        $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
        $pageSize = (int) $pageSize == 0 ? 20 : $pageSize;
        $dataProvider->getPagination()->setPageSize($pageSize);
        return $this->render('index', compact('searchModel', 'dataProvider', 'pageSize'));
    }
}
