<?php
namespace app\modules\admin\controllers;

use app\models\Notification;
use app\models\NotificationResult;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;

class NotifyController extends Controller {

    public function actionIndex() {
        if(\Yii::$app->request->isPost) {
            $post = \Yii::$app->request->post();

            $notify = new Notification();
            $notify->message    =   ArrayHelper::getValue($post, "message");
            $notify->payload    =   ArrayHelper::getValue($post, "payload");
            $notify->tm_create  =   new Expression("NOW()");
            $notify->tm_send    =   new Expression("NOW()");
            if($notify->save()) {
                $whom = ArrayHelper::getValue($post, "whom");
                switch ($whom) {
                    case "all":
                        $users = \Yii::$app->db->createCommand("SELECT id, token FROM users u WHERE AND u.token is NOT NULL")->queryAll();
                        break;
                    default:
                        $users = \Yii::$app->db->createCommand("SELECT u.id, token FROM users u LEFT JOIN subs s ON s.user_id = u.id WHERE s.id is null AND u.token is NOT NULL")->queryAll();
                        break;
                }
                //$users = \Yii::$app->db->createCommand("SELECT u.id, token FROM users u WHERE id = 79630")->queryAll();
                foreach($users as $u) {
                    $notifyResult = new NotificationResult();
                    $notifyResult->notify_id = $notify->id;
                    $notifyResult->user_id = ArrayHelper::getValue($u, "id");
                    $notifyResult->status = 0;
                    $notifyResult->save();
                }

                $tokensData = ArrayHelper::getColumn($users, "token");
                for($i = 0; $i <= ceil(count($tokensData)/100); $i++) {
                    $tokens = array_slice($tokensData, $i * 100, 100);
                    $fields = [
                            'x'         => $i,
                            'id'        => $notify->id,
                            'message'   => $notify->message,
                            'payload'   => $notify->payload,
                            'tokens'    => Json::encode($tokens)
                    ];
                    $fields_string = http_build_query($fields);

                    $ch = curl_init();

                    curl_setopt($ch, CURLOPT_URL,"http://apinomer.com:9999/send");
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    $response = curl_exec ($ch);

                    curl_close ($ch);
                }


            }

            return $this->redirect(["notify/index"]);
        }


        $dataProvider = new ActiveDataProvider([
            "query" => Notification::find()->orderBy(["id" => SORT_DESC])
        ]);

        return $this->render("index", [
            "dataProvider" => $dataProvider
        ]);
    }

    public function actionStatus() {
        $id     = \Yii::$app->request->get("id");
        $token  = \Yii::$app->request->get("token");
        $status = \Yii::$app->request->get("status");

        $user = User::find()->where(["token" => $token])->one();
        $result = NotificationResult::find()->where(["user_id" => $user->id, "notify_id" => $id])->one();
        $result->status = $status;
        $result->save();
        return "OK";
    }

    public function actionSend() {
        /*
        $tokens = \Yii::$app->db->createCommand("SELECT token FROM users u LEFT JOIN subs s ON s.user_id = u.id WHERE s.id is null AND u.token is NOT NULL")->queryColumn();

        $payload = [
            "message" => [
                "item" => "com.wcaller.Wcaller.sub.month.5",
                "title" => "Получите премиум доступ",
                "- Полная проверка по всем базам\n- Включено 10 проверок в месяц\n- Скидка 30% на покупку проверок!"
            ]
        ];


        $fields = [
            'message' => "Мы начислили вам 2 бесплатных поиска, попробуйте наше приложение, уверены оно вам понравится!",
            'payload' => Json::encode($payload),
            'tokens' => Json::encode($tokens)
        ];
        $fields_string = http_build_query($fields);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,"http://nomer.io:9999/send");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec ($ch);

        curl_close ($ch);

        print_r($tokens);
        */
    }
}