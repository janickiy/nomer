<?php

namespace app\modules\admin\controllers;

use app\models\Payment;
use app\models\UserSub;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\db\Query;

class PaymentsController extends AdminController
{
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Payment::find()
                ->orderBy(['id' => SORT_DESC])
                ->where(['>=', 'tm', date('Y-m-d 00:00:00', strtotime('-7 day'))])
                ->andWhere(["<>", "type_id", Payment::TYPE_TESTAPPLE])
                ->orderBy(['tm' => SORT_DESC])
                ->with(["site", "user", "user.payments"])
        ]);

        $dataProvider->pagination = false;

        $todaySubs = UserSub::find()->where(new Expression(
            "extract(month from tm_purchase) = ".date("m")." 
                    AND extract(year from tm_purchase) = ".date("Y")." 
                    AND extract(day from tm_purchase) = ".date("d")
        ))->count(1);

        return $this->render('index', [
            'dataProvider'  => $dataProvider,
            'todaySubs'     => $todaySubs
        ]);
    }
}
