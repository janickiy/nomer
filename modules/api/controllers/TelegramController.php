<?php

namespace app\modules\api\controllers;

use app\components\SearchHelper;
use app\models\RequestResult;
use app\models\ResultCache;
use app\models\SearchRequest;
use app\models\Settings;
use app\models\TmpVk;
use app\models\Vk;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\rest\Controller;
use yii\web\Response;

class TelegramController extends Controller
{
    public function actionIndex($phone)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $searchRequest = new SearchRequest();
        $searchRequest->phone = $phone;
        $searchRequest->source_id = SearchRequest::SOURCE_TELEGRAM;
        $searchRequest->save();

        $id = $searchRequest->id;

        $result = [
            "phones"    => [$phone],
            "emails"    => [],
            "profiles"  => [],
            "public"    => [],
            "phone"     => $phone,
            "open"      => [],
            "elements"  => [],
            "valid"     => []
        ];

        $profiles = [];

        $ch = curl_init('http://ssd.nomer.io/api/'.$phone.'?token=NWBpdeqbbAFJMVYJU6XAfhyydeyhgX');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if($httpCode == 200) { // Все ок, берем данные
            $response = Json::decode($response);
            foreach($response as $r)  {
                switch($r["type"]) {
                    case "phone":
                        $result["phones"][] = $r["data"];
                        break;
                    case "profile_id":
                        $result["profiles"][] = $r["data"];
                        $profiles[] = $r["data"];
                        if(isset($r["isValid"]) && $r["isValid"] == 1) {
                            $result["valid"][] = $r["data"];
                        }
                        break;
                    case "email":
                        if (strpos($r["data"], '@') !== false) {
                            $result["emails"][] = $r["data"];
                        }
                        break;
                }
            }
        }

        $result["phones"] = array_unique($result["phones"]);

        foreach($result["phones"] as $_phone) {
            $vk = TmpVk::find()->where(['phone' => $phone])->all();
            foreach($vk as $v) {
                $profile_id = $v["id"];
                $result["public"][] = $profile_id;
                $profiles[] = $profile_id;
            }
        }

        $vkrows = Vk::find()->where(["or",
            ["phone1" => $phone],
            ["phone2" => $phone],
        ])->all();

        foreach ($vkrows as $vkrow) {
            $profiles[] = $vkrow["id"];
//            $result["public"][] = $vkrow["id"];
            $result["open"][] = $vkrow["id"];
            $result["phones"][] = $vkrow["phone1"];
            $result["phones"][] = $vkrow["phone2"];
            $result["phones"][] = $vkrow["phone3"];
            $result["phones"][] = $vkrow["phone4"];
        }

        $result["emails"] = array_unique($result["emails"]);
        $result["phones"] = array_unique(array_filter($result["phones"]));

        $phones = $result['phones'];

        // Получаем оператора и регион
        $operator = SearchHelper::Operator($searchRequest->phone);

        $requestResult = RequestResult::find()->where(["request_id" => $id, "type_id" => ResultCache::TYPE_OPERATOR])->one();

        if(is_null($requestResult)) {
            $requestResult = new RequestResult();
            $requestResult->request_id = $id;
            $requestResult->type_id = ResultCache::TYPE_OPERATOR;
            $requestResult->data = Json::encode($operator);
            $requestResult->index = 0;
            $requestResult->save();
        }

        $items = [];

        // Telegram
        $telegramItems = SearchHelper::Telegram($phones);

        $requestResult = RequestResult::find()->where(["request_id" => $id, "type_id" => ResultCache::TYPE_TELEGRAM])->one();

        if(is_null($requestResult)) {
            $requestResult = new RequestResult();
            $requestResult->request_id = $id;
            $requestResult->type_id = ResultCache::TYPE_TELEGRAM;
            $requestResult->data = Json::encode($telegramItems);
            $requestResult->index = count($telegramItems)?Settings::get("search_index_telegram", 7):0;
            $requestResult->save();
        }

        $items = ArrayHelper::merge($items, $telegramItems);

        // Viber
        $viberItems = SearchHelper::Viber($phones);

        $requestResult = RequestResult::find()->where(["request_id" => $id, "type_id" => ResultCache::TYPE_VIBER])->one();

        if(is_null($requestResult)) {
            $requestResult = new RequestResult();
            $requestResult->request_id = $id;
            $requestResult->type_id = ResultCache::TYPE_VIBER;
            $requestResult->data = Json::encode($viberItems);
            $requestResult->index = count($viberItems)?Settings::get("search_index_viber", 7):0;
            $requestResult->save();
        }

        $items = ArrayHelper::merge($items, $viberItems);

        // Numbuster
        $numbusterItems = SearchHelper::Numbuster($phones);

        $requestResult = RequestResult::find()->where(["request_id" => $id, "type_id" => ResultCache::TYPE_NUMBUSTER])->one();

        if(is_null($requestResult)) {
            $requestResult = new RequestResult();
            $requestResult->request_id = $id;
            $requestResult->type_id = ResultCache::TYPE_NUMBUSTER;
            $requestResult->data = Json::encode($numbusterItems);
            $requestResult->index = count($numbusterItems)?Settings::get("search_index_numbuster", 7):0;
            $requestResult->save();
        }

        $items = ArrayHelper::merge($items, $numbusterItems);

        // Truecaller
        $truecallerItems = SearchHelper::Truecaller($phones);

        $requestResult = RequestResult::find()->where(["request_id" => $id, "type_id" => ResultCache::TYPE_TRUECALLER])->one();

        if(is_null($requestResult)) {
            $requestResult = new RequestResult();
            $requestResult->request_id = $id;
            $requestResult->type_id = ResultCache::TYPE_TRUECALLER;
            $requestResult->data = Json::encode($truecallerItems);
            $requestResult->index = count($truecallerItems)?Settings::get("search_index_truecaller", 7):0;
            $requestResult->save();
        }

        $items = ArrayHelper::merge($items, $truecallerItems);

        // Vk
        $vkItems = SearchHelper::Vk($phones);

        $requestResult = RequestResult::find()->where(["request_id" => $id, "type_id" => ResultCache::TYPE_VK_2012])->one();

        if(is_null($requestResult)) {
            $requestResult = new RequestResult();
            $requestResult->request_id = $id;
            $requestResult->type_id = ResultCache::TYPE_VK_2012;
            $requestResult->data = Json::encode($vkItems);
            $requestResult->index = count($vkItems)?Settings::get("search_index_vk", 20):0;
            $requestResult->save();
        }

        $items = ArrayHelper::merge($items, $vkItems);

        // Avito
        $avitoItems = SearchHelper::Avito($phones);

        $requestResult = RequestResult::find()->where(["request_id" => $id, "type_id" => ResultCache::TYPE_AVITO])->one();

        if(is_null($requestResult)) {
            $requestResult = new RequestResult();
            $requestResult->request_id = $id;
            $requestResult->type_id = ResultCache::TYPE_AVITO;
            $requestResult->data = Json::encode($avitoItems);
            $requestResult->index = count($avitoItems)?Settings::get("search_index_avito", 15):0;
            $requestResult->save();
        }

        $items = ArrayHelper::merge($items, $avitoItems);

        if(count(ArrayHelper::getColumn($items, "name"))) {
            $searchRequest->is_has_name = true;
        }
        if(count(ArrayHelper::getColumn($items, "photo"))) {
            $searchRequest->is_has_photo = true;
        }
        $searchRequest->save();

        sort($items);

        $names = array_unique(array_filter(array_map(function ($data) {
            return ArrayHelper::getValue($data, 'name');
        }, $items)));

        sort($names);

        $photo = false;

        foreach ($items as $item) {
            if (isset($item['photo'])) {
                $photo = $item['photo'];
                break;
            }
        }

        return compact('names', 'photo');
    }
}
