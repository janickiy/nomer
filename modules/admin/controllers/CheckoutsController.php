<?php
namespace app\modules\admin\controllers;

use app\models\Checkout;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\web\Controller;

class CheckoutsController extends AdminController {

    public function actionIndex() {
        $dataProvider = new ActiveDataProvider([
            "query" => Checkout::find()->where(["tm_done" => null])
        ]);

        return $this->render("index", ["dataProvider" => $dataProvider]);
    }

    public function actionDone($id) {
        $checkout = Checkout::find()->where(["id" => $id])->one();
        $checkout->tm_done = new Expression("NOW()");
        $checkout->save();

        return $this->redirect(["index"]);
    }
}