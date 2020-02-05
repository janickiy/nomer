<?php
namespace  app\modules\api\controllers;

use app\models\User;
use Yii;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;

class NotifyController extends Controller {

    public function actionIndex($token) {
        $uuid = Yii::$app->getRequest()->getHeaders()->get('uuid', false);
        if(!$uuid) {
            throw new BadRequestHttpException();
        }

        $user = User::find()->where(["uuid" => $uuid])->one();

        if($user) {
            $user->token = $token;
            if(!$user->save()) {
                return $user->getErrors();
            } else {
                return true;
            }

        } else {
            return false;
        }
    }
}