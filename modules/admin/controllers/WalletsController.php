<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use app\models\Wallet;
use yii\web\NotFoundHttpException;

class WalletsController extends AdminController
{
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Wallet::find()->orderBy(['type_id' => SORT_ASC, 'balance' => SORT_DESC])->where(["status" => 1])
        ]);
        $dataProvider->pagination = false;
        return $this->render('index', compact('dataProvider'));
    }

    public function actionCreate()
    {
        $model = new Wallet();

        if (Yii::$app->getRequest()->getIsPost() && $model->load(Yii::$app->request->post()) && $model->save()) {
            $this->redirect(['wallets/index']);
        }

        return $this->render('create', compact('model'));
    }

    public function actionView($id)
    {
        $model = Wallet::find()->where(compact('id'))->one();

        if (!$model) throw new NotFoundHttpException('Кошелёк не найден.');

        if (Yii::$app->getRequest()->getIsPost() && $model->load(Yii::$app->request->post()) && $model->save()) {
                $this->redirect(['wallets/index']);
        }

        return $this->render('view', compact('model'));
    }
}
