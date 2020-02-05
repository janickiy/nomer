<?php

namespace app\modules\admin\controllers;

use app\models\Payment;
use app\models\SearchRequest;
use Yii;
use yii\data\ActiveDataProvider;
use app\models\Site;
use yii\db\Expression;

class SitesController extends AdminController
{
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Site::find()->orderBy("id")->where(["not in", "id", [6, 14]])
        ]);

        $dataProvider->pagination = false;

        $payments = Payment::find()->select(["site_id", new Expression("SUM(amount) as sum")])->where([">=", "tm", date("Y-m-d H:i:s", strtotime("-30 days"))])->groupBy("site_id")->indexBy("site_id")->asArray()->all();
        $seaches = SearchRequest::find()->select(["site_id", new Expression("count(1) as count")])->where([">=", "tm", date("Y-m-d H:i:s", strtotime("-30 days"))])->groupBy("site_id")->indexBy("site_id")->asArray()->all();
        //$applePayments = SearchRequest::find()->select(["site_id", new Expression("count(1) as count")])->where([">=", "tm", date("Y-m-d H:i:s", strtotime("-30 days"))])->groupBy("site_id")->indexBy("site_id")->asArray()->all();

        return $this->render('index', compact('dataProvider', 'payments', 'seaches'));
    }

    public function actionCreate()
    {
        $model = new Site();

        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            return $this->redirect(['sites/index']);
        }

        return $this->render('create', compact('model'));
    }

    public function actionUpdate($id)
    {
        $model = Site::find()->where(compact('id'))->one();

        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            return $this->redirect(['sites/index']);
        }

        return $this->render('update', compact('model'));
    }

    public function actionDelete($id)
    {
        $model = Site::find()->where(compact('id'))->one();

        $model->delete();

        return $this->redirect(['sites/index']);
    }

    public function actionSetDemo($id)
    {
        $model = Site::find()->where(compact('id'))->one();
        $model->is_demo = !$model->is_demo;
        $model->save();
        return $this->redirect(Yii::$app->getRequest()->getReferrer());
    }
}
