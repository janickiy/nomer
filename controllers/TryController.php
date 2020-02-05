<?php
namespace app\controllers;

use app\models\User;
use app\models\UserFingerprint;
use app\models\UserTest;
use Yii;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;

class TryController extends Controller {

    public function actionIndex() {
        /*
        if(\Yii::$app->request->isPost) {
            $phone = \Yii::$app->request->post("phone");
            $phone = preg_replace('/[\D]/', '', $phone);

            $code = rand(0, 9999);
            $code = sprintf("%'.04d", $code);

            $user = User::find()->where(["id" => \Yii::$app->getUser()->getId()])->one();
            $user->phone = $phone;
            $user->code = $code;
            if($user->save()) {
                $url = "https://smsc.ru/sys/send.php?login=admeo&psw=admeosmsc&phones=$phone&mes=".urlencode("Ваш код: $code")."&charset=utf-8&sender=nomer.io";
                /*
                    Url::to(["@smsc",
                    'login'     => 'admeo',
                    'psw'       => 'admeosmsc',
                    'phones'    => $phone,
                    'mes'       => 'Ваш код: '.$code,
                    'charset'   => 'utf-8',
                    'sender'    => 'nomer.io'
                ]);
                *

                file_get_contents($url);
            }


            return $this->redirect(["try/check"]);
        }

        if(\Yii::$app->getUser()->getIdentity()->is_test) {
            return $this->goHome();
        }

        return $this->render("index");
        */
    }

    public function actionCheck() {
        /*
        if(\Yii::$app->request->isPost) {
            $code = \Yii::$app->request->post("code");
            $code = preg_replace('/\D/', '', $code);
            $user = User::find()->where(["id" => \Yii::$app->getUser()->getId()])->one();

            if($user->code == $code) {
                $test = UserTest::find()->where(["ip" => \Yii::$app->request->getUserIP()])->one();

                if(!$user->is_test) {
                    $user->is_test = true;
                    $user->checks += 5;
                    if($test) {
                        $user->status = 0;
                        $user->ban = User::BAN_IP;
                    } else {
                        $hashes = ArrayHelper::getColumn(UserFingerprint::find()->where(["user_id" => \Yii::$app->getUser()->getId()])->all(), "hash");
                        $checks = UserFingerprint::find()->where(["<>", "user_id", \Yii::$app->getUser()->getId()])->andWhere(["hash" => $hashes])->all();
                        if(count($checks)) {
                            $user->status = 0;
                            $user->ban = User::BAN_FINGERPRINT;
                        }
                    }
                    if($user->save()) {
                        $test = new UserTest();
                        $test->user_id = $user->id;
                        $test->tm = new Expression('NOW()');
                        $test->ip = \Yii::$app->request->getUserIP();
                        $test->save();
                    }
                }
                return $this->goHome();
            }
        }

        return $this->render("check");
        */
    }
}