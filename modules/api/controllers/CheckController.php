<?php
namespace app\modules\api\controllers;

use app\models\TmpVk;
use app\models\Vk;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;

class CheckController extends Controller {

    private $tokens = [
        "vMnP2BEx1vlKk7cLAKCBgbNVuBArl3xb" => "antiparkon"
    ];

    public function actionIndex($phone, $token) {
        if(!array_key_exists($token, $this->tokens)) throw new ForbiddenHttpException("Bad token");

        if(!preg_match("/^79([0-9]{9})$/", $phone))  throw new BadRequestHttpException("Invalid phone number");

        return ["success" => 1];

        $success = 0;
        $ch = curl_init('http://ssd.nomer.io/api/' . $phone . '?token=NWBpdeqbbAFJMVYJU6XAfhyydeyhgX');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $phones = [];
        if ($httpCode == 200) { // Все ок, берем данные
            $response = Json::decode($response);
            foreach ($response as $r) {
                if(isset($r["type"])) {
                    switch ($r["type"]) {
                        case "profile_id":
                            $success = 1;
                            break;
                        case "phone":
                            $phones[] = $r["data"];
                            break;
                    }
                }

            }
        }

        if($success) {
            return ["success" => $success];
        }

        $ids = [];

        foreach ($phones as $_phone) {
            $vk = TmpVk::find()->where(['phone' => $_phone])->all();
            if(count($vk)) {
                $success = 1;
                $ids = ArrayHelper::merge($ids, ArrayHelper::getColumn($vk, "id"));
            }
        }

        $vkrows = Vk::find()->where(["or",
            ["phone1" => $phone],
            ["phone2" => $phone],
        ])->all();

        $ids = ArrayHelper::merge($ids, ArrayHelper::getColumn($vkrows, "id"));

        if(count($ids)) {
            $success = 1;
        }
        $photos = [];

        $socData = @file_get_contents("https://api.vk.com/method/users.get?user_ids=" . join(",", $ids) . "&fields=photo_id,photo_max,photo_max_orig&lang=ru&access_token=8f95fab19fb8d3d41bdeeb28f0112cb2cd3c86a93fc66acbd29f327d1aa3f196540bfe10dcd4ca97baf37");
        if($socData) {
            $socData = Json::decode($socData);
            $socData = $socData["response"];

            $photos = ArrayHelper::getColumn($socData, "photo_max_orig");
        }

        return ["success" => $success, "photos" => $photos];
    }
}
