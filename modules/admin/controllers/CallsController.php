<?php

namespace app\modules\admin\controllers;

use app\models\Call;
use yii\data\ActiveDataProvider;

class CallsController extends AdminController
{
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Call::find()->with('organization')->where(['status' => 'dtmf-1']),
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        return $this->render('index', compact('dataProvider'));
    }
}
