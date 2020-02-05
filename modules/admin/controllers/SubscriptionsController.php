<?php
namespace app\modules\admin\controllers;

use app\models\UserSub;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\web\User;

class SubscriptionsController extends AdminController {

    public function actionIndex() {
        $dataProvider = new ActiveDataProvider([
            "query" => UserSub::find()
        ]);

        return $this->render("index", [
            "dataProvider" => $dataProvider
        ]);
    }

    public function actionTest() {
        $dataProvider = new ActiveDataProvider([
            "query" => UserSub::find()->from(UserSub::tableName()." s")->innerJoin(UserSub::tableName()." s2", "s.original_transaction_id = s2.original_transaction_id")->where(new Expression("s.tm_expires::date - s.tm_purchase::date = 3"))->andWhere(new Expression("s2.tm_expires::date - s2.tm_purchase::date > 5"))
        ]);

        return $this->render("index", [
            "dataProvider" => $dataProvider
        ]);
    }


}