<?php
namespace app\modules\api\controllers;

use app\models\User;
use app\models\UserSub;
use Yii;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;

class InfoController extends Controller {

    public function actionIndex() {
        $userId = \Yii::$app->getRequest()->get("id", false);
        $uuid = Yii::$app->getRequest()->getHeaders()->get('uuid', false);
        if(!$uuid && !$userId) {
            throw new BadRequestHttpException();
        }
        if($userId) {
            $user = User::find()->select(["id", "balance", "checks", "email"])->where(["id" => $userId])->one();
        } else {
            $user = User::find()->select(["id", "balance", "checks", "email"])->where(["uuid" => $uuid])->one();
        }
        $isAndroid = Yii::$app->getRequest()->getHeaders()->get('isandroid', false);
        if(!$user && !$isAndroid) {
            $user = new User();
            $user->email = null;
            $user->uuid = $uuid;
            $user->save();

            $user = User::find()->select(["id", "balance", "checks", "email"])->where(["uuid" => $uuid])->one();
        }

        $sub = UserSub::find()->where(["user_id" => $user->id])->andWhere([">=", "tm_expires", new Expression("NOW()")])->orderBy(["tm_expires" => SORT_DESC])->one();

        $expire = ArrayHelper::getValue($sub, "tm_expires", null);
        $checks = $user->checks;
        if(strtotime($expire) < time() && $checks < 0) {
            $user->checks = 0;
            $user->save();
            $checks = 0;
        }
        if($checks < 0) $checks = -1;

        return [
            "id"        => $user->id,
            "balance"   => $user->balance,
            "checks"    => $checks,
            "email"     => $user->email,
            "isSubscribe" => $expire?1:0,
            "subscribe" => $expire?Yii::$app->formatter->asDate($expire, 'd MMMM'):null
        ];
    }
}