<?php
namespace app\modules\api\controllers;

use app\models\TmpVk;
use app\models\Vk;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\rest\Controller;

class PonomeruController extends Controller {

    public function actionIndex($phone) {
        if(!in_array(\Yii::$app->request->getUserIP(), ["46.20.39.121", "82.204.203.174"])) die("Превед медвед :)");
        $phone = preg_replace("/\D/", "", $phone);
        $phone = preg_replace("/^8/", "7", $phone);

        $response = [];

        $profiles = [];

        $vkOpen = Vk::find()->where(["or", ["phone1" => $phone], ["phone2" => $phone]])->all();

        $vk2012 = TmpVk::find()->where(['phone' => $phone])->all();

        $profiles = ArrayHelper::merge($profiles, ArrayHelper::getColumn($vkOpen, "id"));
        $profiles = ArrayHelper::merge($profiles, ArrayHelper::getColumn($vk2012, "id"));

        if(count($profiles)) {
            $socData = @file_get_contents("https://api.vk.com/method/users.get?user_ids=" . join(",", $profiles) . "&fields=photo_id,photo_max,photo_max_orig&lang=ru&access_token=8f95fab19fb8d3d41bdeeb28f0112cb2cd3c86a93fc66acbd29f327d1aa3f196540bfe10dcd4ca97baf37");
            if($socData) {
                $socData = Json::decode($socData);
                $socData = $socData["response"];
                foreach($socData as $p) {
                    $row = [
                        "id" => ArrayHelper::getValue($p, "uid"),
                        "name" => ArrayHelper::getValue($p, "first_name")." ".ArrayHelper::getValue($p, "last_name"),
                    ];
                    $photo = @file_get_contents(ArrayHelper::getValue($p, "photo_max_orig"));
                    if($photo) {
                        $row["photo"] = base64_encode($photo);
                    }

                    $response[] = $row;
                }
            }
        }


        return $response;
    }
}