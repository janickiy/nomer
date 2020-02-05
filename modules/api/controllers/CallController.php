<?php
namespace app\modules\api\controllers;

use app\models\Call;
use app\models\User;
use Yii;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\rest\Controller;

class CallController extends Controller {

    public function actionIndex() {
        $body = \Yii::$app->request->getRawBody();
        $body = Json::decode($body);

        $call = new Call();
        $call->cuid = ArrayHelper::getValue($body, 'cuid');
        $call->status = ArrayHelper::getValue($body, 'status');
        $call->duration = ArrayHelper::getValue($body, 'duration');
        $call->phone = ArrayHelper::getValue($body, 'number');
        $call->tm = new Expression('NOW()');
        $call->save();

        if($call->status == "dtmf-1") {
            $user = User::find()->where(["email" => $call->phone])->one();
            if(!$user) {
                $code = sprintf("%'.04d", rand(0, 9999));

                $user = new User();
                $user->email = $call->phone;
                $user->password = $code;
                $user->auth_key = \Yii::$app->getSecurity()->generateRandomString();
                $user->checks = 3;

                if($user->save()) {
                    $user->checks = 3;
                    $user->save();
                    $url = Url::to(['https://smsc.ru/sys/send.php',
                        'login'     => 'admeo',
                        'psw'       => 'admeosmsc',
                        'phones'    => $call->phone,
                        'mes'       => "Probiv nomera telefona(3 besplatnye proverki):\nhttps://num.gg\nlogin:".$user->email."\npass:".$user->password,
                        'charset'   => 'utf-8',
                        'sender'    => 'num.gg'
                    ], 'https');
                    file_get_contents($url);
                }
            }
        }


    }
}