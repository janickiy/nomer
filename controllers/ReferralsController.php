<?php
namespace app\controllers;

use app\models\Checkout;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

class ReferralsController extends Controller {

    public function actionIndex() {
        if(\Yii::$app->getUser()->isGuest) return $this->goHome();
        $dataProvider = new ActiveDataProvider([
            'query' => User::find()->where(["ref_id" => \Yii::$app->getUser()->getId()])
        ]);

        $todayUsers = User::find()->where(["ref_id" => \Yii::$app->getUser()->id])->andWhere([">=", "tm_create", date("Y-m-d 00:00:00")])->count(1);
        $totalUsers = User::find()->where(["ref_id" => \Yii::$app->getUser()->id])->count(1);

        $users = User::find()->where(["ref_id" => \Yii::$app->getUser()->id])->with(["payments"])->all();
        $payments = ArrayHelper::getColumn($users, "payments.sum");
        $sum = array_sum($payments);

        return $this->render("index", [
            "dataProvider"  => $dataProvider,
            "todayUsers"    => $todayUsers,
            "totalUsers"    => $totalUsers,
            "sum"           => $sum * 0.3
        ]);
    }

    public function actionCheckout() {
        if(\Yii::$app->getUser()->isGuest) {
            return $this->goHome();
        }
        $wallet = \Yii::$app->request->get("wallet");

        /* @var $user \app\models\User */
        $user = \Yii::$app->getUser()->getIdentity();
        if($user->ref_balance < 5000) {
            return $this->redirect(["referrals/index"]);
        }

        $checkout = new Checkout();
        $checkout->user_id = $user->id;
        $checkout->wallet = $wallet;
        $checkout->sum = $user->ref_balance;
        $checkout->tm_create = new Expression('NOW()');
        if($checkout->save()) {
            $user->ref_balance = 0;
            $user->save();
        }
        return $this->redirect(["referrals/index"]);
    }

    public function actionNew($id) {
        \Yii::$app->session->set("ref_id", join("~", [$id, time()]));
        if(!\Yii::$app->getUser()->isGuest) {
            return $this->goHome();
        }

        return $this->redirect(["/", '#' => 'signup']);
    }
}