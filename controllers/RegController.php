<?php
namespace app\controllers;

use app\models\User;
use yii\db\Expression;
use yii\helpers\Url;
use yii\web\Response;

class RegController extends \yii\web\Controller {

    public function actionIndex() {
        return $this->render("index");
    }

    public function actionSms() {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $phone = \Yii::$app->request->get("phone");

        $phone = preg_replace("/[^\d]/", "", $phone);
        $code = \Yii::$app->getSecurity()->generateRandomString(6);

        $user = User::find()->where(["phone" => $phone])->one();
        if(is_null($user)) {
            $user = new User();
            $user->phone = $phone;
            $user->tm_create = new Expression("NOW()");
        } else {
            $user->tm_update = new Expression("NOW()");
        }

        $user->auth_key = \Yii::$app->getSecurity()->generateRandomString();
        $user->code = $code;
        if($user->save()) {
            $url = Url::to(["https://smsc.ru/sys/send.php",
                'login'     => 'admeo',
                'psw'       => 'admeosmsc',
                'phones'    => $phone,
                'mes'       => 'Ваш код: '.$code,
                'charset'   => 'utf-8',
                'sender'    => \Yii::$app->name
            ], "https");

            file_get_contents($url);
        } else {
            return ["error" => 1];
        }

        return ["error" => 0];
    }

    public function actionCheck() {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $code = \Yii::$app->request->get("code");

        $user = User::findByCode($code);
        if(!is_null($user)) {
            $user->tm_last_auth = new Expression("NOW()");
            $user->save();
            \Yii::$app->user->login($user, 3600 * 24 * 30);
            return ["error" => 0];
        }

        return ["error" => 1];
    }
}
