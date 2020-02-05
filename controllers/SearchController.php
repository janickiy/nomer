<?php

namespace app\controllers;

use app\components\FakeHelper;
use app\components\SearchHelper;
use app\components\SearchJob;
use app\models\BlockPhone;
use app\models\Gibdd;
use app\models\Proxy;
use app\models\TmpVk;
use app\models\Token;
use app\models\UrlFilter;
use app\models\User;
use app\models\UserSub;
use app\models\Vk;
use app\models\VkRaw;
use Exception;
use Yii;
use app\models\Facebook;
use app\models\File;
use app\models\RequestResult;
use app\models\ResultCache;
use app\models\SearchRequest;
use app\models\Settings;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\helpers\Html;

class SearchController extends Controller
{
    protected $tokens = [
        // ['ip', 'truecaller', 'numbuster']
        ['5.199.134.79', 'ErqH2RfLL_X2UubBtc_jt8VKF3cXtsic', '61n2phbfyn40s8k4g40gg4ocokck8wwsg40gokk0wsowcoswo4'], // 79253499567n@gmail.com:nomerio123
        ['37.157.254.253', 'bGJ6WkDMnFa28s8ndi4eOe57H3cXw09r', '515b8ve3o144o40s88og80gscogk4g0go44wsg4kwkcswg0sog'], // 79254336764
        ['37.157.254.254', 'HM~J_5AbOT1lQAt_XB9Ryol353cXxT15', '1lqq9vw7marokkgscggwskosoo8gswo0wc04kokwws04g0skgk'], // 79253483749
        ['217.79.191.72', '-eMqbxzUV1P-SK_Grs9z5AJI43cYB6U2', '1lqq9vw7marokkgscggwskosoo8gswo0wc04kokwws04g0skgk'], // 79254335795
        ['217.79.191.73', 'RRgxoy2HdIMC4Rg2S2SOWLruT3cYB~He', '5jhtny6rsrok4484o48skcowk40ccsgo8wog8w840w48kkkk48'] // 79254336563
    ];

    protected $fb = [
        'apinomer.com',
        'srv-1.apinomer.com',
        'srv-2.apinomer.com',
        'srv-3.apinomer.com',
        'srv-4.apinomer.com',
        'srv-5.apinomer.com',
    ];

    public $enableCsrfValidation = false;

    public function actionPhone()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $phone = \Yii::$app->request->get('phone');
        $phone = preg_replace('/\D/', '', $phone);

        if (mb_strlen($phone) == 10) {
            $phone = '7' . $phone;
        } else {
            $phone = preg_replace('/^8/', '7', $phone);
        }

        $source = null;

        switch (Yii::$app->getRequest()->getHeaders()->get('source')) {
            case 'android':
                $source = SearchRequest::SOURCE_ANDROID;
                break;
            case 'iOS':
                $source = SearchRequest::SOURCE_IOS;
                break;
        }

        $uuid = Yii::$app->getRequest()->getHeaders()->get('uuid');

        /* @var $user User */
        $user = User::find()->where(compact('uuid'))->one();

        if (!$user) {
            $user = new User();
            $user->uuid = $uuid;
            $user->save();
        }

        /* @var $searchRequests SearchRequest[] */
        $searchRequests = SearchRequest::find()->with('results')->where(['user_id' => $user->id])->all();

        $results = 0;

        foreach ($searchRequests as $request) {
            $names = [];
            $photos = [];

            foreach ($request->results as $result) {
                $data = Json::decode($result->data);

                if ($data && is_array($data)) {
                    try {
                        $names = ArrayHelper::merge($names, ArrayHelper::getColumn($data, "name"));
                        $photos = ArrayHelper::merge($photos, ArrayHelper::getColumn($data, "photo"));
                    } catch (Exception $e) {

                    }
                }
            }

            if (array_filter($names) || array_filter($photos)) $results++;
        }

        if ($results >= 5) {
            return ['id' => 0];
        }

        $searchRequest = new SearchRequest();
        $searchRequest->ip = \Yii::$app->request->userIP;
        $searchRequest->ua = \Yii::$app->request->userAgent;
        $searchRequest->phone = $phone;
        $searchRequest->tm = new Expression('NOW()');
        $searchRequest->user_id = $user->id;
        $searchRequest->source_id = $source;
        $searchRequest->save();

        $result = [
            'id' => $searchRequest->id
        ];

        $operatorCache = ResultCache::find()->where(['phone' => $phone, 'type_id' => ResultCache::TYPE_OPERATOR])->one();

        if (is_null($operatorCache)) {
            $operator = @file_get_contents('https://moscow.megafon.ru/api/mfn/info?msisdn=' . $phone);

            if ($operator) {
                $operator = Json::decode($operator);

                if (!is_null($operator) && !isset($operator['error'])) {
                    $result['mobile']['operator'] = $operator['operator'];
                    $result['mobile']['region'] = $operator['region'];

                    $operatorCache = new ResultCache();
                    $operatorCache->phone = $phone;
                    $operatorCache->type_id = ResultCache::TYPE_OPERATOR;
                    $operatorCache->data = Json::encode($result['mobile']);
                    $operatorCache->save();
                }
            }
        } else {
            $result['mobile'] = Json::decode($operatorCache->data);
        }

        return $result;
    }

    public function actionScoristaStart($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $searchRequest = SearchRequest::getDb()->cache(function () use ($id) {
            return SearchRequest::find()->where(['id' => $id])->one();
        });

        $uuid = 0;

        if ($searchRequest->user_id && $searchRequest->user->is_vip) {

            $phone = $searchRequest->phone;

            $result = @file_get_contents("http://89.175.178.114:8989/start/" . $phone);
            if ($result) {
                $result = Json::decode($result);
                $status = ArrayHelper::getValue($result, "status");
                if($status == 200) {
                    $uuid = ArrayHelper::getValue($result, "id");
                }
            }
        }

        return ["id" => $uuid];
    }

    public function actionScoristaResult($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $searchRequest = SearchRequest::getDb()->cache(function () use ($id) {
            return SearchRequest::find()->where(['id' => $id])->one();
        });

        $data = \Yii::$app->request->post();
        $uuid = ArrayHelper::getValue($data, "uuid");

        $result = @file_get_contents("http://89.175.178.114:8989/getResult/" . $uuid);

        $f = fopen(Yii::getAlias('@runtime').'/scorista.log', 'a+');
        fwrite($f, $result."\n\n\n");
        fclose($f);

        try {
            $result = Json::decode($result);

        } catch (Exception $e) {
            return ["status" => 404, "view" => "Ошибка в данных"];
        }


        if(ArrayHelper::getValue($result, "status") == 100) {
            return ["status" => 100];
        }

        $scoristaResult = null;

        if(ArrayHelper::getValue($result, "status") == 200) {
            $requestResult = RequestResult::find()->where(["request_id" => $id, "type_id" => ResultCache::TYPE_SCORISTA])->one();

            if (is_null($requestResult)) {
                $requestResult = new RequestResult();
                $requestResult->request_id = $id;
                $requestResult->type_id = ResultCache::TYPE_SCORISTA;
                $requestResult->data = Json::encode(ArrayHelper::getValue($result, "data"));
                $requestResult->index = 0;
                $requestResult->save();
            }

            $scoristaResult = $requestResult->data;

            return ["status" => 200, "view" => $this->renderAjax("scorista", ["searchRequest" => $searchRequest, "scoristaResult" => $scoristaResult])];
        }

        return ["status" => 404, "view" => "Ничего не найдено"];
    }

    public function actionFreeStart($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $post = \Yii::$app->request->post();

        $phones = $post["phones"];

        $jobID = Yii::$app->queue->push(new SearchJob([
            'id' => $id,
            'phones' => $phones,
        ]));

        $jobCount = `/home/nomer.io/www/yii queue/info | grep waiting | grep -o '[0-9]*'`;

        return ["jobID" => $jobID, "jobCount" => $jobCount];
    }

    public function actionFreeResult($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $post = \Yii::$app->request->post();

        $jobID = ArrayHelper::getValue($post, "jobID", 0);
        if (!$jobID) {
            $phones = $post["phones"];

            $jobID = Yii::$app->queue->push(new SearchJob([
                'id' => $id,
                'phones' => $phones,
            ]));
        }
        $jobCount = ArrayHelper::getValue($post, "jobCount", 0);

        if (Yii::$app->queue->isWaiting($jobID)) {
            return ["status" => 0, "count" => $jobCount];
        } elseif (Yii::$app->queue->isDone($jobID)) {
            $searchRequest = SearchRequest::find()->where(["id" => $id])->one();
            \Yii::$app->cache->delete("phone-" . $searchRequest->phone);
            $items = [];
            $results = RequestResult::find()->where(["request_id" => $id])->andWhere(["<>", "type_id", 1])->all();
            foreach ($results as $result) {
                $resultItems = @Json::decode($result->data);
                $items = ArrayHelper::merge($items, $resultItems);
            }

            if (count(ArrayHelper::getColumn($items, "name"))) {
                $searchRequest->is_has_name = true;
            }
            if (count(ArrayHelper::getColumn($items, "photo"))) {
                $searchRequest->is_has_photo = true;
            }
            $searchRequest->save();

            $operatorRow = RequestResult::find()->where(["request_id" => $id])->andWhere(["type_id" => 1])->one();
            $operator = Json::decode($operatorRow->data);

            return [
                "view" => $this->renderAjax("free", [
                    "operator" => $operator,
                    "items" => $items,
                    "searchRequest" => $searchRequest
                ]),
            ];
        }
        return ["status" => -1];
    }

    public function actionPopup($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $searchRequest = SearchRequest::getDb()->cache(function () use ($id) {
            return SearchRequest::find()->where(['id' => $id])->one();
        });

        $post = \Yii::$app->request->post();

        $phones = $post["phones"];

        // Получаем оператора и регион
        $operator = SearchHelper::Operator($searchRequest->phone);

        $requestResult = RequestResult::find()->where(["request_id" => $id, "type_id" => ResultCache::TYPE_OPERATOR])->one();

        if (is_null($requestResult)) {
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

        if (is_null($requestResult)) {
            $requestResult = new RequestResult();
            $requestResult->request_id = $id;
            $requestResult->type_id = ResultCache::TYPE_TELEGRAM;
            $requestResult->data = Json::encode($telegramItems);
            $requestResult->index = count($telegramItems) ? Settings::get("search_index_telegram", 7) : 0;
            $requestResult->save();
        }

        $items = ArrayHelper::merge($items, $telegramItems);

        // Numbuster
        $numbusterItems = SearchHelper::Numbuster($phones);

        $requestResult = RequestResult::find()->where(["request_id" => $id, "type_id" => ResultCache::TYPE_NUMBUSTER])->one();

        if (is_null($requestResult)) {
            $requestResult = new RequestResult();
            $requestResult->request_id = $id;
            $requestResult->type_id = ResultCache::TYPE_NUMBUSTER;
            $requestResult->data = Json::encode($numbusterItems);
            $requestResult->index = count($numbusterItems) ? Settings::get("search_index_numbuster", 7) : 0;
            $requestResult->save();
        }

        $items = ArrayHelper::merge($items, $numbusterItems);

        // Truecaller
        $truecallerItems = SearchHelper::Truecaller($phones);

        $requestResult = RequestResult::find()->where(["request_id" => $id, "type_id" => ResultCache::TYPE_TRUECALLER])->one();

        if (is_null($requestResult)) {
            $requestResult = new RequestResult();
            $requestResult->request_id = $id;
            $requestResult->type_id = ResultCache::TYPE_TRUECALLER;
            $requestResult->data = Json::encode($truecallerItems);
            $requestResult->index = count($truecallerItems) ? Settings::get("search_index_truecaller", 7) : 0;
            $requestResult->save();
        }

        $items = ArrayHelper::merge($items, $truecallerItems);

        // Vk
        $vkItems = SearchHelper::Vk($phones);

        $requestResult = RequestResult::find()->where(["request_id" => $id, "type_id" => ResultCache::TYPE_VK_2012])->one();

        if (is_null($requestResult)) {
            $requestResult = new RequestResult();
            $requestResult->request_id = $id;
            $requestResult->type_id = ResultCache::TYPE_VK_2012;
            $requestResult->data = Json::encode($vkItems);
            $requestResult->index = count($vkItems) ? Settings::get("search_index_vk", 20) : 0;
            $requestResult->save();
        }

        $items = ArrayHelper::merge($items, $vkItems);

        /*
        $viberItems = SearchHelper::Viber($phones);

        $requestResult = RequestResult::find()->where(["request_id" => $id, "type_id" => ResultCache::TYPE_VIBER])->one();

        if (is_null($requestResult)) {
            $requestResult = new RequestResult();
            $requestResult->request_id = $id;
            $requestResult->type_id = ResultCache::TYPE_VIBER;
            $requestResult->data = Json::encode($viberItems);
            $requestResult->index = count($viberItems) ? Settings::get("search_index_viber", 7) : 0;
            $requestResult->save();
        }

        $items = ArrayHelper::merge($items, $viberItems);
        */

        $avitoItems = SearchHelper::Avito($phones);

        $requestResult = RequestResult::find()->where(["request_id" => $id, "type_id" => ResultCache::TYPE_AVITO])->one();

        if (is_null($requestResult)) {
            $requestResult = new RequestResult();
            $requestResult->request_id = $id;
            $requestResult->type_id = ResultCache::TYPE_AVITO;
            $requestResult->data = Json::encode($avitoItems);
            $requestResult->index = count($avitoItems) ? Settings::get("search_index_avito", 15) : 0;
            $requestResult->save();
        }

        $items = ArrayHelper::merge($items, $avitoItems);

        if (count(ArrayHelper::getColumn($items, "name"))) {
            $searchRequest->is_has_name = true;
        }
        if (count(ArrayHelper::getColumn($items, "photo"))) {
            $searchRequest->is_has_photo = true;
        }
        $searchRequest->save();

        if (in_array($searchRequest->source_id, [SearchRequest::SOURCE_ANDROID, SearchRequest::SOURCE_IOS])) {
            sort($items);
            return [
                "elements" => $items
            ];
        }

        return [
            "view" => $this->renderAjax("free", [
                "operator" => $operator,
                "items" => $items,
                "searchRequest" => $searchRequest
            ]),
            "elements" => []
        ];
    }

    public function actionBasic($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        if (!$id) return [];

        $searchRequest = SearchRequest::getDb()->cache(function () use ($id) {
            return SearchRequest::find()->where(['id' => $id])->one();
        });

        $is_cache = \Yii::$app->request->get("is_cache", 0);

        $phone = $searchRequest->phone;

        $block = BlockPhone::find()->where(["phone" => $searchRequest->phone, "status" => 2])->one();
        if ($block && !ArrayHelper::getValue($searchRequest, "user", "is_admin")) {
            return [
                "phones" => [],
                "emails" => [],
                "profiles" => [],
                "public" => [],
                "phone" => $phone,
                "open" => [],
                "elements" => [],
                "valid" => [],
                "is_cache" => $is_cache
            ];
        }

        $result = [
            "phones" => [$phone],
            "emails" => [],
            "profiles" => [],
            "public" => [],
            "phone" => $phone,
            "open" => [],
            "elements" => [],
            "valid" => [],
            "is_cache" => $is_cache
        ];

        $profiles = [];
        $years = [];

        $ch = curl_init('http://ssd.nomer.io/api/' . $phone . '?token=NWBpdeqbbAFJMVYJU6XAfhyydeyhgX');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode == 200) { // Все ок, берем данные
            $response = Json::decode($response);
            foreach ($response as $r) {
                if (isset($r["type"])) {
                    switch ($r["type"]) {
                        case "phone":
                            $result["phones"][] = $r["data"];
                            break;
                        case "profile_id":
                            $result["profiles"][] = $r["data"];
                            $profiles[] = $r["data"];
                            if (isset($r["isValid"]) && $r["isValid"] == 1) {
                                $result["public"][] = $r["data"];
                            }
                            break;
                        case "email":
                            if (strpos($r["data"], '@') !== false) {
                                $result["emails"][] = $r["data"];
                            }
                            break;
                        case "birthday":
                            $year = $r["data"];
                            $yearRows = explode(".", $year);
                            foreach ($yearRows as $yearRow) {
                                if (strlen($yearRow) == 4) {
                                    $years[] = $yearRow;
                                }
                            }
                            break;
                    }
                }

            }
        }

        $result["phones"] = array_unique($result["phones"]);

        foreach ($result["phones"] as $_phone) {
            $vk = TmpVk::find()->where(['phone' => $_phone])->all();
            foreach ($vk as $v) {
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
            //$result["public"][] = $vkrow["id"];
            $result["open"][] = $vkrow["id"];
            $result["phones"][] = $vkrow["phone1"];
            $result["phones"][] = $vkrow["phone2"];
            $result["phones"][] = $vkrow["phone3"];
            $result["phones"][] = $vkrow["phone4"];
        }

        $result["emails"] = array_unique($result["emails"]);
        $result["phones"] = array_unique(array_filter($result["phones"]));

        $elements = [];
        $years = array_unique($years);
        $realYears = [];
        if (count($years) && (max($years) - min($years) >= 5)) {
            $realYears = [min($years)];
        } else {
            $realYears = $years;
        }

        if ($searchRequest->user_id && $searchRequest->user->is_vip) {
            foreach ($result["emails"] as $e) {
                $elements[] = ["name" => $e];
            }
            foreach ($result["phones"] as $p) {
                $elements[] = ["name" => $p];
            }
            foreach ($realYears as $year) {
                $age = date("Y") - $year;
                if ($age < 60 && $age > 17) {
                    $elements[] = ["name" => "Предполагаемый возраст: " . $age];
                }

            }
        } else {
            foreach ($realYears as $year) {
                $age = date("Y") - $year;
                if ($age < 60 && $age > 17) {
                    $elements[] = ["name" => "Предполагаемый возраст: " . ($age - 1) . " - " . ($age + 1)];
                }
            }
        }
        $result["elements"] = $elements;

        $fake = FakeHelper::getPhone($phone);
        if (!is_null($fake)) {
            $result = $fake;
        }

        if ($searchRequest->is_payed == 0) $result["free"] = true;
        /*
        if(in_array($searchRequest->source_id, [SearchRequest::SOURCE_IOS, SearchRequest::SOURCE_ANDROID])) {
            $result["is_mobile"] = true;
        }
        */

        return $result;
    }

    public function actionOperator($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $searchRequest = SearchRequest::getDb()->cache(function () use ($id) {
            return SearchRequest::find()->where(['id' => $id])->one();
        });

        $phone = $searchRequest->phone;
        $result = [];

        if (preg_match("/79(\d{9})/", $phone)) {
            $operatorCache = ResultCache::find()->where(["phone" => $phone, "type_id" => ResultCache::TYPE_OPERATOR])->one();
            if (is_null($operatorCache)) {
                $ch = curl_init("https://moscow.megafon.ru/api/mfn/info?msisdn=" . $phone);
                curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1000);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($httpCode == 200) { // Все ок, берем данные
                    $operator = Json::decode($response);

                    if (!is_null($operator) && !isset($operator["error"])) {
                        $result["operator"] = $operator["operator"];
                        $result["region"] = $operator["region"];
                        $operatorCache = new ResultCache();
                        $operatorCache->phone = $phone;
                        $operatorCache->type_id = ResultCache::TYPE_OPERATOR;
                        $operatorCache->data = Json::encode($result);
                        $operatorCache->save();
                    }
                }
            } else {
                $result = Json::decode($operatorCache->data);
            }

            $requestResult = RequestResult::find()->where(["request_id" => $id, "type_id" => ResultCache::TYPE_OPERATOR])->one();
            if (is_null($requestResult)) {
                $requestResult = new RequestResult();
                $requestResult->request_id = $id;
                $requestResult->type_id = ResultCache::TYPE_OPERATOR;
                $requestResult->data = Json::encode($result);
                $requestResult->index = $result ? Settings::get("search_index_operator", 5) : 0;

                if ($operatorCache) {
                    $requestResult->cache_id = $operatorCache->id;
                }

                $requestResult->save();
            }
        }

        return $result;
    }

    public function actionInstagram($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $searchRequest = SearchRequest::getDb()->cache(function () use ($id) {
            return SearchRequest::find()->where(['id' => $id])->one();
        });

        $data = \Yii::$app->request->post();
        $is_cache = ArrayHelper::getValue($data, "is_cache", 0);

        $phone = $searchRequest->phone;

        $is_mobile = ArrayHelper::getValue($data, "is_mobile", 0);

        if ($phone == "79999999988") {
            if (!$is_mobile && !in_array($searchRequest->source_id, [SearchRequest::SOURCE_ANDROID, SearchRequest::SOURCE_IOS])) {
                return [
                    "elements" => [
                        [
                            'name' => Html::tag("i", "", ["class" => "instagram"]) . " " . Html::a('instagram: ivanova18284', "https://www.instagram.com/ivanova18284/", ["target" => "_blank"]),
                            'photo' => FakeHelper::getPhoto("inst")
                        ]
                    ],
                    "view" => $this->renderAjax("instagram", ["result" => [["link" => "https://www.instagram.com/ivanova18284/", "name" => "ivanova18284"]]]),
                    "index" => 20,
                ];
            } else {
                return [
                    "elements" => [
                        [
                            'name' => 'ivanova18284',
                            'link' => 'https://www.instagram.com/ivanova18284/',
                            'photo' => FakeHelper::getPhoto("inst")
                        ]
                    ],
                    "index" => 20,
                ];
            }

        }
        $result = [];

        $insts = [];

        if ($searchRequest->refresh && !$is_cache) {
            $instagramCache = null;
        } else {
            $instagramCache = ResultCache::find()->where(['phone' => $phone, 'type_id' => ResultCache::TYPE_INSTAGRAM])->orderBy(["id" => SORT_DESC])->one();
        }

        if (is_null($instagramCache)) {
            $data = \Yii::$app->request->post();

            $profiles = [];

            if (isset($data['public'])) {
                foreach ($data['public'] as $p) {
                    $profiles[] = $p;
                }
            }

            if (isset($data['profiles'])) {
                foreach ($data['profiles'] as $p) {
                    $profiles[] = $p;
                }
            }

            $profiles = array_unique($profiles);

            $vkrows = Vk::find()->where(['id' => $profiles])->all();

            foreach ($vkrows as $vk) {
                $instagramUsername = trim($vk['instagram']);

                if (!in_array($instagramUsername, $insts)) {
                    $insts[] = $instagramUsername;
                }
            }

            foreach ($profiles as $p) {
                $instagramUsername = SearchHelper::Instagram($p);

                if (!is_null($instagramUsername) && !in_array($instagramUsername, $insts)) {
                    $insts[] = $instagramUsername;
                }
            }

            /*
            $fb = RequestResult::find()->where(["request_id" => $id, "type_id" => ResultCache::TYPE_FACEBOOK])->one();
            if ($fb) {
                $fbItems = Json::decode($fb->data);
                foreach ($fbItems as $item) {
                    if (isset($item["id"])) {
                        $instagramUsername = @file_get_contents('http://127.0.0.1:1122/instagram/' . $item["id"]);

                        if (!in_array($instagramUsername, $insts)) {
                            $insts[] = $instagramUsername;
                        }
                    }
                }
            }
            */

            $instagramProfiles = [];

            foreach ($insts as $username) {
                if (trim($username) == "") continue;
                $profile = [
                    'username' => $username,
                    'link' => 'https://www.instagram.com/' . $username . '/'
                ];

                try {
                    $ch = curl_init('https://www.instagram.com/' . $username . '/');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_TIMEOUT_MS, 3000);
                    $body = curl_exec($ch);
                    $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);

                    if ($responseCode != 200) continue;

                    preg_match('/"full_name":"(.*?)"/u', $body, $matches);


                    if (count($matches) == 2) {
                        $t = json_decode('{"name":"' . $matches[1] . '"}');
                        $profile['name'] = $t->name;
                    }

                    if ($profile['name'] == "") {
                        $profile['name'] = $username;
                    }

                    preg_match('/"profile_pic_url":"(.*?)"/', $body, $matches);

                    if (count($matches) == 2) {
                        $profile['photo'] = base64_encode(file_get_contents(str_replace('/s150x150', '', $matches[1])));
                    }
                } catch (\Exception $e) {
                }

                $instagramProfiles[] = $profile;
            }

            $insts = $instagramProfiles;

            $instagramCache = new ResultCache();
            $instagramCache->phone = $phone;
            $instagramCache->type_id = ResultCache::TYPE_INSTAGRAM;
            $instagramCache->data = Json::encode($insts, JSON_UNESCAPED_UNICODE);
            $instagramCache->save();
        } else {
            $insts = Json::decode($instagramCache->data);
        }

        $requestResult = RequestResult::find()->where(["request_id" => $id, "type_id" => ResultCache::TYPE_INSTAGRAM])->one();
        if (is_null($requestResult)) {
            $requestResult = new RequestResult();
            $requestResult->request_id = $id;
            $requestResult->type_id = ResultCache::TYPE_INSTAGRAM;
            $requestResult->data = Json::encode($insts, JSON_UNESCAPED_UNICODE);
            $requestResult->index = Settings::get("search_index_instagram", 5) * count($insts);
            if (!is_null($instagramCache)) {
                $requestResult->cache_id = $instagramCache->id;
            }
            $requestResult->save();
        }


        foreach ($insts as $i) {
            if (!$is_mobile && !in_array($searchRequest->source_id, [SearchRequest::SOURCE_ANDROID, SearchRequest::SOURCE_IOS])) {
                $result['elements'][] = [
                    'name' => Html::tag("i", "", ["class" => "instagram"]) . " " . Html::a('instagram: ' . $i['username'], $i['link'], ["target" => "_blank"]),
                ];
            }
        }
        foreach ($insts as $i) {
            $iItem = [];
            if (isset($i["name"])) {
                $iItem['name'] = $i['username'];
            }
            if (isset($i["photo"])) {
                $iItem['photo'] = $i['photo'];
            }
            if (isset($i["link"])) {
                $iItem['link'] = $i['link'];
            }
            $result['elements'][] = $iItem;
        }

        if (isset($result["elements"]) && count($result["elements"])) $result["index"] = Settings::get("search_index_instagram", 20);

        if (count(ArrayHelper::getColumn($result, "name"))) {
            $searchRequest->is_has_name = true;
        }
        if (count(ArrayHelper::getColumn($result, "photo"))) {
            $searchRequest->is_has_photo = true;
        }
        $searchRequest->save();

        return [
            "view" => $this->renderAjax("instagram", ["result" => $insts]),
            "index" => count($insts) ? Settings::get("search_index_instagram", 20) : 0,
            "elements" => $insts
        ];
    }

    public function actionVk($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $searchRequest = SearchRequest::getDb()->cache(function () use ($id) {
            return SearchRequest::find()->where(['id' => $id])->one();
        });

        $data = \Yii::$app->request->post();
        $is_cache = ArrayHelper::getValue($data, "is_cache", 0);

        /* @var $user User */
        $user = User::find()->where(['id' => $searchRequest->user_id])->one();
        if (!$user || !ArrayHelper::getValue($user, 'is_vip', false)) throw new ForbiddenHttpException('Нет доступа');

        $phone = $searchRequest->phone;
        $result = [];

        if ($searchRequest->refresh && !$is_cache) {
            $vkCache = null;
        } else {
            $vkCache = ResultCache::find()->where(['phone' => $phone, 'type_id' => ResultCache::TYPE_VK])->one();
        }

        if (is_null($vkCache)) {
            if (isset($data["profiles"]) && is_array($data["profiles"])) foreach ($data["profiles"] as $profile_id) {
                $socData = file_get_contents("https://api.vk.com/method/users.get?user_ids=" . $profile_id . "&fields=photo_id,photo_max,photo_max_orig&lang=ru&access_token=d585cf50d585cf50d585cf5077d5d94150dd585d585cf508cbd309b41310c9fdc6c13d7&v=5.62");
                $socData = Json::decode($socData);
                $socData = $socData["response"][0];
                $names = [$socData["first_name"], $socData["last_name"]];
                $item = [
                    "id" => $profile_id,
                    "name" => join(" ", $names),
                    "link" => "https://vk.com/id" . $profile_id
                ];

                if (isset($socData["photo_id"])) {
                    $photoData = file_get_contents("https://api.vk.com/method/photos.getById?photos=" . ArrayHelper::getValue($socData, "photo_id") . "&lang=ru&access_token=d585cf50d585cf50d585cf5077d5d94150dd585d585cf508cbd309b41310c9fdc6c13d7&v=5.60");
                    $photoData = Json::decode($photoData);
                    $photoData = $photoData["response"][0];
                    $pUrl = ArrayHelper::getValue($photoData, "photo_2560", false);
                    if (!$pUrl) $pUrl = ArrayHelper::getValue($photoData, "photo_1280", false);
                    if (!$pUrl) $pUrl = ArrayHelper::getValue($photoData, "photo_130", false);
                    $big = @file_get_contents($pUrl);

                    if ($big) {
                        $big = base64_encode($big);
                        $tmp = "/tmp/" . $profile_id . ".jpg";
                        $this->base64_to_jpeg($big, $tmp);

                        $file_path_str = '/vk/' . $profile_id . '.jpg';
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, "http://qq.apinomer.com/upload" . $file_path_str);

                        curl_setopt($ch, CURLOPT_PUT, 1);

                        $fh_res = fopen($tmp, 'r');

                        curl_setopt($ch, CURLOPT_INFILE, $fh_res);
                        curl_setopt($ch, CURLOPT_INFILESIZE, filesize($tmp));

                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_BINARYTRANSFER, TRUE); // --data-binary

                        $curl_response_res = curl_exec($ch);
                        fclose($fh_res);
                        @unlink($tmp);
                        $item["photo"] = "https://q.apinomer.com" . $file_path_str;
                    }
                } else {
                    $big = @file_get_contents(ArrayHelper::getValue($socData, "photo_max_orig"));
                    if ($big) {
                        $big = base64_encode($big);
                        $tmp = "/tmp/" . $profile_id . ".jpg";
                        $this->base64_to_jpeg($big, $tmp);

                        $file_path_str = '/vk/' . $profile_id . '.jpg';
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, "http://q.apinomer.com/upload" . $file_path_str);

                        curl_setopt($ch, CURLOPT_PUT, 1);

                        $fh_res = fopen($tmp, 'r');

                        curl_setopt($ch, CURLOPT_INFILE, $fh_res);
                        curl_setopt($ch, CURLOPT_INFILESIZE, filesize($tmp));

                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_BINARYTRANSFER, TRUE); // --data-binary

                        $curl_response_res = curl_exec($ch);
                        fclose($fh_res);
                        @unlink($tmp);
                        $item["photo"] = "https://q.apinomer.com" . $file_path_str;
                    }
                }
                if (isset($data["raw"])) $item["raw"] = reset($data["raw"]);

                $result[$profile_id] = $item;
            }
            $vkCache = new ResultCache();
            $vkCache->phone = $phone;
            $vkCache->type_id = ResultCache::TYPE_VK;
            $vkCache->data = Json::encode($result);
            $vkCache->save();
        } else {
            $result = Json::decode($vkCache->data);
        }

        $requestResult = RequestResult::find()->where(["request_id" => $id, "type_id" => ResultCache::TYPE_VK])->one();
        if (is_null($requestResult)) {
            $requestResult = new RequestResult();
            $requestResult->request_id = $id;
            $requestResult->type_id = ResultCache::TYPE_VK;
            $requestResult->data = Json::encode($result);
            $requestResult->index = count($result) ? Settings::get("search_index_vk_vip", 15) : 0;
            if (!is_null($vkCache)) {
                $requestResult->cache_id = $vkCache->id;
            }
            $requestResult->save();
        }

        sort($result);
        $is_mobile = ArrayHelper::getValue($data, "is_mobile", 0);
        if ($is_mobile) {
            foreach ($result as $id => $r) {
                if (!isset($r["photo"])) continue;
                if (preg_match("/https/", $r["photo"])) {
                    $p = @file_get_contents($r["photo"]);
                    if ($p) {
                        $r["photo"] = base64_encode($p);
                    }
                    $result[$id] = $r;
                }
            }

            return [
                "index" => count($result) ? Settings::get("search_index_vk_vip", 15) : 0,
                "elements" => $result
            ];
        }

        return [
            "view" => $this->renderAjax("vk", [
                "searchRequest" => $searchRequest,
                "result" => $result,
                "phone" => preg_replace("/^7/", "8", $phone),
            ]),
            "index" => count($result) ? Settings::get("search_index_vk_vip", 15) : 0,
            "elements" => $result
        ];
    }

    public function actionVk2012($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $searchRequest = SearchRequest::getDb()->cache(function () use ($id) {
            return SearchRequest::find()->where(['id' => $id])->one();
        });

        $data = \Yii::$app->request->post();
        $is_cache = ArrayHelper::getValue($data, "is_cache", 0);

        $isGuest = is_null($searchRequest->user_id);

        $phone = $searchRequest->phone;

        if ($phone == "79999999988") {

            $result = [["id" => 11676514, "name" => "Маша Иванова", "photo" => FakeHelper::getPhoto("vk")]];
            return [
                "view" => $this->renderAjax("vk", [
                    "searchRequest" => $searchRequest,
                    "result" => $result,
                    "phone" => preg_replace("/^7/", "8", $phone)
                ]),
                "index" => count($result) ? Settings::get("search_index_vk", 15) : 0,
                "elements" => $result
            ];
        }
        $result2012 = $resultOpen = [];

        if ($searchRequest->refresh && !$is_cache) {
            $vkCache = null;
        } else {
            $vkCache = ResultCache::find()->where(['phone' => $phone, 'type_id' => ResultCache::TYPE_VK_2012])->one();
        }

        if (is_null($vkCache)) {
            if (isset($data["public"]) && is_array($data["public"])) foreach ($data["public"] as $profile_id) {
                $socData = file_get_contents("https://api.vk.com/method/users.get?user_ids=" . $profile_id . "&fields=photo_id,photo_max,photo_max_orig&lang=ru&access_token=d585cf50d585cf50d585cf5077d5d94150dd585d585cf508cbd309b41310c9fdc6c13d7&v=5.62");
                $socData = Json::decode($socData);
                $socData = $socData["response"][0];
                $names = [$socData["first_name"], $socData["last_name"]];
                $item = [
                    "id" => $profile_id,
                    "name" => join(" ", $names),
                    "link" => "https://vk.com/id" . $profile_id
                ];

                if (isset($socData["photo_id"])) {
                    $photoData = file_get_contents("https://api.vk.com/method/photos.getById?photos=" . ArrayHelper::getValue($socData, "photo_id") . "&lang=ru&access_token=d585cf50d585cf50d585cf5077d5d94150dd585d585cf508cbd309b41310c9fdc6c13d7&v=5.60");
                    $photoData = Json::decode($photoData);
                    $photoData = $photoData["response"][0];
                    $pUrl = ArrayHelper::getValue($photoData, "photo_2560", false);
                    if (!$pUrl) $pUrl = ArrayHelper::getValue($photoData, "photo_1280", false);
                    if (!$pUrl) $pUrl = ArrayHelper::getValue($photoData, "photo_130", false);
                    $big = @file_get_contents($pUrl);
                    if ($big) {
                        $big = base64_encode($big);
                        $tmp = "/tmp/" . $profile_id . ".jpg";
                        $this->base64_to_jpeg($big, $tmp);

                        $file_path_str = '/vk2012/' . $profile_id . '.jpg';
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, "http://qq.apinomer.com/upload" . $file_path_str);

                        curl_setopt($ch, CURLOPT_PUT, 1);

                        $fh_res = fopen($tmp, 'r');

                        curl_setopt($ch, CURLOPT_INFILE, $fh_res);
                        curl_setopt($ch, CURLOPT_INFILESIZE, filesize($tmp));

                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_BINARYTRANSFER, TRUE); // --data-binary

                        $curl_response_res = curl_exec($ch);
                        fclose($fh_res);
                        @unlink($tmp);
                        $item["photo"] = "https://q.apinomer.com" . $file_path_str;
                    }
                } else {
                    $big = @file_get_contents(ArrayHelper::getValue($socData, "photo_max_orig"));
                    if ($big) {
                        $big = base64_encode($big);
                        $tmp = "/tmp/" . $profile_id . ".jpg";
                        $this->base64_to_jpeg($big, $tmp);

                        $file_path_str = '/vk2012/' . $profile_id . '.jpg';
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, "http://q.apinomer.com/upload" . $file_path_str);

                        curl_setopt($ch, CURLOPT_PUT, 1);

                        $fh_res = fopen($tmp, 'r');

                        curl_setopt($ch, CURLOPT_INFILE, $fh_res);
                        curl_setopt($ch, CURLOPT_INFILESIZE, filesize($tmp));

                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_BINARYTRANSFER, TRUE); // --data-binary

                        $curl_response_res = curl_exec($ch);
                        fclose($fh_res);
                        @unlink($tmp);
                        $item["photo"] = "https://q.apinomer.com" . $file_path_str;
                    }
                }
                if (!$isGuest && $searchRequest->user->is_admin) {
                    $vkRaw = VkRaw::find()->where(["id" => $profile_id])->one();
                    if ($vkRaw) {
                        $item["raw"] = $vkRaw->data;
                    }
                }
                //if(isset($data["raw"])) $item["raw"] = reset($data["raw"]);

                $result2012[$profile_id] = $item;
            }
            $vkCache = new ResultCache();
            $vkCache->phone = $phone;
            $vkCache->type_id = ResultCache::TYPE_VK_2012;
            $vkCache->data = Json::encode($result2012);
            $vkCache->save();
        } else {
            $result2012 = Json::decode($vkCache->data);
        }

        $requestResult = RequestResult::find()->where(["request_id" => $id, "type_id" => ResultCache::TYPE_VK_2012])->one();
        if (is_null($requestResult)) {
            $requestResult = new RequestResult();
            $requestResult->request_id = $id;
            $requestResult->type_id = ResultCache::TYPE_VK_2012;
            $requestResult->data = Json::encode($result2012);
            $requestResult->index = count($result2012) ? Settings::get("search_index_vk", 15) : 0;
            if (!is_null($vkCache)) {
                $requestResult->cache_id = $vkCache->id;
            }
            $requestResult->save();
        }

        // OPEN DATA

        if ($searchRequest->refresh) {
            $vkOpenCache = null;
        } else {
            $vkOpenCache = ResultCache::find()->where(['phone' => $phone, 'type_id' => ResultCache::TYPE_VK_OPEN])->one();
        }

        if (is_null($vkOpenCache)) {
            $data = \Yii::$app->request->post();
            if (isset($data["open"]) && is_array($data["open"])) foreach ($data["open"] as $profile_id) {
                $socData = @file_get_contents("https://api.vk.com/method/users.get?user_ids=" . $profile_id . "&fields=photo_id,photo_max,photo_max_orig&lang=ru&access_token=d585cf50d585cf50d585cf5077d5d94150dd585d585cf508cbd309b41310c9fdc6c13d7&v=5.62");
                if ($socData) {
                    $socData = Json::decode($socData);
                    $socData = $socData["response"][0];
                    $names = [$socData["first_name"], $socData["last_name"]];
                    $item = [
                        "id" => $profile_id,
                        "name" => join(" ", $names),
                        "link" => "https://vk.com/id" . $profile_id
                    ];

                    if (isset($socData["photo_id"])) {
                        $photoData = file_get_contents("https://api.vk.com/method/photos.getById?photos=" . ArrayHelper::getValue($socData, "photo_id") . "&lang=ru&access_token=d585cf50d585cf50d585cf5077d5d94150dd585d585cf508cbd309b41310c9fdc6c13d7&v=5.62");
                        $photoData = Json::decode($photoData);
                        $photoData = $photoData["response"][0];
                        $pUrl = ArrayHelper::getValue($photoData, "photo_2560", false);
                        if (!$pUrl) $pUrl = ArrayHelper::getValue($photoData, "photo_1280", false);
                        if (!$pUrl) $pUrl = ArrayHelper::getValue($photoData, "photo_130", false);
                        $big = @file_get_contents($pUrl);
                        if ($big) {
                            $big = base64_encode($big);
                            $tmp = "/tmp/" . $profile_id . ".jpg";
                            $this->base64_to_jpeg($big, $tmp);

                            $file_path_str = '/vk2012/' . $profile_id . '.jpg';
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, "http://qq.apinomer.com/upload" . $file_path_str);

                            curl_setopt($ch, CURLOPT_PUT, 1);

                            $fh_res = fopen($tmp, 'r');

                            curl_setopt($ch, CURLOPT_INFILE, $fh_res);
                            curl_setopt($ch, CURLOPT_INFILESIZE, filesize($tmp));

                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_BINARYTRANSFER, TRUE); // --data-binary

                            $curl_response_res = curl_exec($ch);
                            fclose($fh_res);
                            @unlink($tmp);
                            $item["photo"] = "https://q.apinomer.com" . $file_path_str;
                        }
                    } else {
                        $big = @file_get_contents(ArrayHelper::getValue($socData, "photo_max_orig"));
                        if ($big) {
                            $big = base64_encode($big);
                            $tmp = "/tmp/" . $profile_id . ".jpg";
                            $this->base64_to_jpeg($big, $tmp);

                            $file_path_str = '/vk2012/' . $profile_id . '.jpg';
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, "http://q.apinomer.com/upload" . $file_path_str);

                            curl_setopt($ch, CURLOPT_PUT, 1);

                            $fh_res = fopen($tmp, 'r');

                            curl_setopt($ch, CURLOPT_INFILE, $fh_res);
                            curl_setopt($ch, CURLOPT_INFILESIZE, filesize($tmp));

                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_BINARYTRANSFER, TRUE); // --data-binary

                            $curl_response_res = curl_exec($ch);
                            fclose($fh_res);
                            @unlink($tmp);
                            $item["photo"] = "https://q.apinomer.com" . $file_path_str;
                        }
                    }
                    if (isset($data["raw"])) $item["raw"] = reset($data["raw"]);

                    $resultOpen[$profile_id] = $item;
                }

            }
            $vkOpenCache = new ResultCache();
            $vkOpenCache->phone = $phone;
            $vkOpenCache->type_id = ResultCache::TYPE_VK_OPEN;
            $vkOpenCache->data = Json::encode($resultOpen);
            $vkOpenCache->save();
        } else {
            $resultOpen = Json::decode($vkOpenCache->data);
        }

        $requestResult = RequestResult::find()->where(["request_id" => $id, "type_id" => ResultCache::TYPE_VK_OPEN])->one();
        if (is_null($requestResult)) {
            $requestResult = new RequestResult();
            $requestResult->request_id = $id;
            $requestResult->type_id = ResultCache::TYPE_VK_OPEN;
            $requestResult->data = Json::encode($resultOpen);
            $requestResult->index = count($resultOpen) ? Settings::get("search_index_vk", 15) : 0;
            if (!is_null($vkOpenCache)) {
                $requestResult->cache_id = $vkOpenCache->id;
            }
            $requestResult->save();
        }

        $result = [];
        foreach ($result2012 as $pid => $r) {
            if (!array_key_exists($pid, $result)) {
                $result[$pid] = $r;
            }
        }

        foreach ($resultOpen as $pid => $r) {
            if (!array_key_exists($pid, $result)) {
                $result[$pid] = $r;
            }
        }

        sort($result);

        if (count(ArrayHelper::getColumn($result, "name"))) {
            $searchRequest->is_has_name = true;
        }
        if (count(ArrayHelper::getColumn($result, "photo"))) {
            $searchRequest->is_has_photo = true;
        }
        $searchRequest->save();

        $is_mobile = ArrayHelper::getValue($data, "is_mobile", 0);
        if ($is_mobile) {
            foreach ($result as $id => $r) {
                if (!isset($r["photo"])) continue;
                if (preg_match("/https/", $r["photo"])) {
                    $p = @file_get_contents($r["photo"]);
                    if ($p) {
                        $r["photo"] = base64_encode($p);
                    }
                    $result[$id] = $r;
                }
            }

            return [
                "index" => count($result) ? Settings::get("search_index_vk", 15) : 0,
                "elements" => $result
            ];
        }

        return [
            "view" => $this->renderAjax("vk", [
                "searchRequest" => $searchRequest,
                "result" => $result,
                "phone" => preg_replace("/^7/", "8", $phone)
            ]),
            "index" => count($result) ? Settings::get("search_index_vk", 15) : 0,
            "elements" => $result
        ];
    }

    private function base64_to_jpeg($base64_string, $output_file)
    {
        // open the output file for writing
        $ifp = fopen($output_file, 'wb');

        // we could add validation here with ensuring count( $data ) > 1
        fwrite($ifp, base64_decode($base64_string));

        // clean up the file resource
        fclose($ifp);

        return $output_file;
    }

    public function actionFacebookResult($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $searchRequest = SearchRequest::getDb()->cache(function () use ($id) {
            return SearchRequest::find()->where(['id' => $id])->one();
        });

        $phone = $searchRequest->phone;

        $result = [];

        $facebookCache = null;

        $data = \Yii::$app->request->post();
        $fbServer = ArrayHelper::getValue($data, "fbServer", "127.0.0.1");

        $ch = curl_init('http://' . $fbServer . '/fb/search/check/' . $data["uuid"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode == 200) {
            $response = trim($response);
            $resultRaw = Json::decode($response);
            if (!isset($resultRaw["progress"])) {
                $resultRaw["progress"] = 0;
            }
            if (!isset($resultRaw["goal"])) {
                $resultRaw["goal"] = 1;
            }
            $progress = $resultRaw["progress"] / $resultRaw["goal"] * 100;
            if ($progress < 100) {
                return ["progress" => round($progress, 2)];
            } else {
                foreach ($resultRaw["result"] as $r) {
                    if (isset($r["ID"])) {
                        $item = [
                            "link" => "https://www.facebook.com/profile.php?id=" . $r["ID"],
                            "name" => isset($r["name"]) ? $r["name"] : "",
                            "photo" => ArrayHelper::getValue($r, "photo")
                        ];
                        $fb = new Facebook();
                        $fb->fb_id = (string)$r["ID"];
                        $fb->name = isset($r["name"]) ? $r["name"] : "";
                        $fb->phone = $r["payload"];
                        $fb->photo = ArrayHelper::getValue($r, "photo");
                        $fb->tm = new Expression('NOW()');
                        if (!$fb->save()) {
                            print_r($fb->getErrors());
                            die();
                        }
                        $result["fb-" . $r["ID"]] = $item;
                    }
                }

                $fbInput = ArrayHelper::merge(isset($data["phones"]) ? $data["phones"] : [], isset($data["emails"]) ? $data["emails"] : []);

                foreach ($fbInput as $fb) {
                    $fbRow = Facebook::find()->where(["phone" => $fb])->one();
                    if (!is_null($fbRow)) {
                        $item = [
                            "id" => $fbRow->fb_id,
                            "link" => "https://www.facebook.com/profile.php?id=" . $fbRow->fb_id,
                            "name" => $fbRow->name,
                            "photo" => $fbRow->photo
                        ];
                        $result["fb-" . $fbRow->fb_id] = $item;
                    }
                }

                $ids = [];
                if (isset($data["public"])) {
                    $ids = ArrayHelper::merge($ids, $data["public"]);
                }
                if (isset($data["profiles"])) {
                    $ids = ArrayHelper::merge($ids, $data["profiles"]);
                }

                if (count($ids) > 0) {
                    $ids = array_unique($ids);
                    $vkrows = Vk::find()->where(["id" => $ids])->all();

                    foreach ($vkrows as $vkrow) {
                        if (trim($vkrow["facebook"]) != '') {
                            $fbId = preg_replace("/\D/", "", $vkrow["facebook"]);
                            $content = @file_get_contents("https://graph.facebook.com/" . $fbId . "?fields=first_name,last_name&access_token=223417934354442|uoEzUVtKfO6Y-txtcgT8i4bzRG8&locale=ru_RU");
                            if ($content) {
                                $content = Json::decode($content);
                                if (!isset($result["fb-" . $fbId])) {
                                    $photo = @file_get_contents("http://graph.facebook.com/" . $fbId . "/picture?width=1500&height=1500");
                                    $result["fb-" . $fbId] = [
                                        "id" => $fbId,
                                        "name" => $content["last_name"] . " " . $content["first_name"],
                                        "link" => "https://www.facebook.com/profile.php?id=" . $fbId
                                    ];
                                    if ($photo) {
                                        $result["fb-" . $fbId]["photo"] = base64_encode($photo);
                                    }
                                }

                            }
                        }
                    }
                }

                if (count($result)) {
                    $facebookCache = new ResultCache();
                    $facebookCache->phone = $phone;
                    $facebookCache->type_id = ResultCache::TYPE_FACEBOOK;
                    $facebookCache->data = Json::encode($result);
                    $facebookCache->save();
                }

                $requestResult = RequestResult::find()->where(["request_id" => $id, "type_id" => ResultCache::TYPE_FACEBOOK])->one();
                if (is_null($requestResult)) {
                    $requestResult = new RequestResult();
                    $requestResult->request_id = $id;
                    $requestResult->type_id = ResultCache::TYPE_FACEBOOK;
                    $requestResult->data = Json::encode($result);
                    $requestResult->index = count($result) ? Settings::get("search_index_fb", 15) : 0;
                    if (!is_null($facebookCache)) {
                        $requestResult->cache_id = $facebookCache->id;
                    }
                    $requestResult->save();
                }

                sort($result);

                if (count(ArrayHelper::getColumn($result, "name"))) {
                    $searchRequest->is_has_name = true;
                }
                if (count(ArrayHelper::getColumn($result, "photo"))) {
                    $searchRequest->is_has_photo = true;
                }
                $searchRequest->save();

                $is_mobile = ArrayHelper::getValue($data, "is_mobile", 0);
                if ($is_mobile) return [
                    "index" => count($result) ? Settings::get("search_index_fb", 15) : 0,
                    "elements" => $result
                ];

                return [
                    "view" => $this->renderAjax("facebook", [
                        "result" => $result,
                        "searchRequest" => $searchRequest
                    ]),
                    "index" => count($result) ? Settings::get("search_index_fb", 15) : 0,
                    "elements" => $result
                ];
            }
        }
    }

    public function actionFacebook($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $searchRequest = SearchRequest::getDb()->cache(function () use ($id) {
            return SearchRequest::find()->where(['id' => $id])->one();
        });

        $phone = $searchRequest->phone;

        $data = \Yii::$app->request->post();
        $is_cache = ArrayHelper::getValue($data, "is_cache", 0);

        if ($phone == "79999999988") {
            $result = [[
                "id" => 100017209713671,
                "name" => "Мария Иванова",
                "link" => "https://www.facebook.com/profile.php?id=100017209713671",
                "photo" => FakeHelper::getPhoto("fb")
            ]];

            return [
                "view" => $this->renderAjax("facebook", [
                    "result" => $result,
                    "searchRequest" => $searchRequest
                ]),
                "index" => count($result) ? Settings::get("search_index_fb", 15) : 0,
                "elements" => $result
            ];
        }

        if ($searchRequest->refresh && !$is_cache) {
            $facebookCache = null;
        } else {
            $facebookCache = ResultCache::find()->where(['phone' => $phone, 'type_id' => ResultCache::TYPE_FACEBOOK])->one();
        }

        $result = [];


        if (is_null($facebookCache)) {
            $fbSearch = [];

            $fbInput = ArrayHelper::merge(isset($data["phones"]) ? $data["phones"] : [], isset($data["emails"]) ? $data["emails"] : []);

            $fbResults = [];

            foreach ($fbInput as $fb) {
                $fbRow = Facebook::find()->where(["phone" => $fb])->one();
                if (is_null($fbRow) || strtotime($fbRow->tm) <= time() - 3600 * 24 * 30) {
                    if (!preg_match('/@/', $fb)) {
//                        $fb = '+'.$fb;
                        $fb = preg_replace("/^7/", "8", $fb);
                    }
                    $fbSearch[] = $fb;
                } else {
                    $fbResults[] = [
                        "id" => $fbRow->fb_id,
                        "name" => $fbRow->name,
                        "photo" => $fbRow->photo,
                        "link" => "https://www.facebook.com/profile.php?id=" . $fbRow->fb_id
                    ];
                }
            }

            $profiles = [];
            if (isset($data['public'])) {
                foreach ($data['public'] as $p) {
                    $profiles[] = $p;
                }
            }

            if (isset($data['profiles'])) {
                foreach ($data['profiles'] as $p) {
                    $profiles[] = $p;
                }
            }

            $profiles = array_unique($profiles);


            foreach ($profiles as $p) {
                $fb = SearchHelper::Facebook($p);
                if (!is_null($fb)) {
                    $fbResults[] = [
                        "id" => $fb["id"],
                        "name" => $fb["name"],
                        "link" => "https://www.facebook.com/profile.php?id=" . $fb["id"]
                    ];
                }

            }

            /*
                        if (count($fbSearch)) {

                            $fbServer = $this->fb[array_rand($this->fb)];
                            $f = fopen(\Yii::getAlias('@runtime') . '/fb.log', "a+");
                            fwrite($f, $phone . " : " . $fbServer . "\n");
                            fclose($f);

                            $ch = curl_init('http://' . $fbServer . '/fb/search');
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, Json::encode($fbSearch));
                            $response = curl_exec($ch);
                            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                            if ($httpCode == 200) { // Все ок, берем данные
                                $result = Json::decode($response);
                                $result["fbServer"] = $fbServer;
                                return $result;
                            }

                        } else {
                            $result = $fbResults;
                        }
                                            */
            $result = $fbResults;

            //$result = $fbResults;
        } else {
            $result = Json::decode($facebookCache->data);
        }

        $requestResult = RequestResult::find()->where(["request_id" => $id, "type_id" => ResultCache::TYPE_FACEBOOK])->one();
        if (is_null($requestResult)) {
            $requestResult = new RequestResult();
            $requestResult->request_id = $id;
            $requestResult->type_id = ResultCache::TYPE_FACEBOOK;
            $requestResult->data = Json::encode($result);
            $requestResult->index = count($result) ? Settings::get("search_index_fb", 15) : 0;
            if (!is_null($facebookCache)) {
                $requestResult->cache_id = $facebookCache->id;
            }
            $requestResult->save();
        }

        sort($result);

        $is_mobile = ArrayHelper::getValue($data, "is_mobile", 0);
        if ($is_mobile) return [
            "index" => count($result) ? Settings::get("search_index_fb", 15) : 0,
            "elements" => $result
        ];

        return [
            "view" => $this->renderAjax("facebook", [
                "result" => $result,
                "searchRequest" => $searchRequest
            ]),
            "index" => count($result) ? Settings::get("search_index_fb", 15) : 0,
            "elements" => $result
        ];
    }

    public function actionViber($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $searchRequest = SearchRequest::getDb()->cache(function () use ($id) {
            return SearchRequest::find()->where(['id' => $id])->one();
        });

        $data = \Yii::$app->request->post();
        $is_cache = ArrayHelper::getValue($data, "is_cache", 0);

        if (is_null($searchRequest->user_id)) throw new ForbiddenHttpException("Нет доступа");

        $phone = $searchRequest->phone;

        $block = BlockPhone::find()->where(["phone" => $searchRequest->phone, "status" => 2])->one();
        if ($block && !ArrayHelper::getValue($searchRequest, "user", "is_admin")) {
            return [];
        }

        if ($phone == "79999999988") {
            return ["index" => 7, "elements" => [["photo" => FakeHelper::getPhoto("viber")]]];
        }

        $result = [];

        $viberCache = ResultCache::find()->where(['phone' => $phone, 'type_id' => ResultCache::TYPE_VIBER])->one();

        if (is_null($viberCache)) {
            $ch = curl_init(SearchHelper::VIBER_ROUTE . mb_substr($phone, 1));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpCode == 200) { // Все ок, берем данные
                $result = Json::decode($response);
                if (count($result) && trim($result["name"]) != "null" && !is_null($result["name"])) {
                    //if ($result["photo"] == "") unset($result["photo"]);

                    $viberCache = new ResultCache();
                    $viberCache->phone = $phone;
                    $viberCache->type_id = ResultCache::TYPE_VIBER;
                    $viberCache->data = Json::encode($result);
                    $viberCache->save();
                }
            }
        } else {
            $result = Json::decode($viberCache->data);
        }

        $requestResult = RequestResult::find()->where(["request_id" => $id, "type_id" => ResultCache::TYPE_VIBER])->orderBy(["id" => SORT_DESC])->one();

        if (is_null($requestResult)) {
            $requestResult = new RequestResult();
            $requestResult->request_id = $id;
            $requestResult->type_id = ResultCache::TYPE_VIBER;
            $requestResult->data = Json::encode($result);
            $requestResult->index = count($result) ? Settings::get("search_index_viber", 7) : 0;
            if (!is_null($viberCache)) {
                $requestResult->cache_id = $viberCache->id;
            }
            $requestResult->save();
        }

        if (!$searchRequest->user_id || !$searchRequest->is_payed) {
            $new = [];

            foreach ($result as $i => $item) {
                if (isset($item["name"])) {
                    $names = preg_split("/ /", $item["name"]);
                    $xnames = [];
                    foreach ($names as $name) {
                        if (mb_strlen($name) > 2) {
                            $xnames[] = mb_substr($name, 0, 1) . str_repeat("*", mb_strlen($name) - 2) . mb_substr($name, -1);
                        } else {
                            $xnames[] = $name;
                        }
                    }
                    $item["name"] = join(" ", $xnames);
                }
                if (isset($item["photo"])) {
                    $imageBlob = base64_decode($item["photo"]);

                    $imagick = new \Imagick();
                    $imagick->readImageBlob($imageBlob);
                    $imagick->blurImage(50, 70);
                    $item["photo"] = $imagick->getImageBlob();
                    $item["photo"] = base64_encode($item["photo"]);
                }

                $new[] = $item;
            }

            $result = $new;
        }

        if ($result == "[]" || !count($result)) {
            $result = [];
        } else {
            $result = [$result];
        }
        return [
            "index" => count($result) ? Settings::get("search_index_viber", 7) : 0,
            "elements" => $result
        ];
    }

    public function actionTelegram($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $searchRequest = SearchRequest::getDb()->cache(function () use ($id) {
            return SearchRequest::find()->where(['id' => $id])->one();
        });

        $data = \Yii::$app->request->post();
        $is_cache = ArrayHelper::getValue($data, "is_cache", 0);

        if (is_null($searchRequest->user_id)) throw new ForbiddenHttpException("Нет доступа");

        $phone = $searchRequest->phone;

        $block = BlockPhone::find()->where(["phone" => $searchRequest->phone, "status" => 2])->one();
        if ($block && !(boolean)ArrayHelper::getValue($searchRequest, ["user", "is_admin"])) {
            return [];
        }

        $result = [];

        if ($searchRequest->refresh && !$is_cache) {
            $telegramCache = null;
        } else {
            $telegramCache = ResultCache::find()->where(['phone' => $phone, 'type_id' => ResultCache::TYPE_TELEGRAM])->orderBy(["id" => SORT_DESC])->one();
        }

        if (is_null($telegramCache)) {
            $doAnti = true;
            if ($searchRequest->source_id == SearchRequest::SOURCE_IOS) {
                $user = $searchRequest->user;
                if ($user) {
                    /* @var $sub \app\models\UserSub */
                    $sub = UserSub::find()->where(["user_id" => $user->id])->andWhere([">=", "tm_expires", new Expression("NOW()")])->orderBy(["tm_expires" => SORT_DESC])->one();
                    if (!$sub || (strtotime($sub->tm_expires) - strtotime($sub->tm_purchase) <= 60 * 60 * 24 * 4)) {
                        $doAnti = false;
                    }
                }
            }
            if ($doAnti) {
                $ch = curl_init('http://apinomer.com:1999/tg/phone/' . $phone);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($httpCode == 200) { // Все ок, берем данные
                    $result = Json::decode($response);
                    $result["name"] = join(" ", array_filter([$result["first_name"], $result["last_name"]]));
                    if (trim($result["photo"]) == "") unset($result["photo"]);
                    if (count($result)) {
                        $telegramCache = new ResultCache();
                        $telegramCache->phone = $phone;
                        $telegramCache->type_id = ResultCache::TYPE_TELEGRAM;
                        $telegramCache->data = Json::encode($result);
                        $telegramCache->save();
                    }
                }
            }
        } else {
            $result = Json::decode($telegramCache->data);
        }

        $requestResult = RequestResult::find()->where(["request_id" => $id, "type_id" => ResultCache::TYPE_TELEGRAM])->one();

        if (is_null($requestResult)) {
            $requestResult = new RequestResult();
            $requestResult->request_id = $id;
            $requestResult->type_id = ResultCache::TYPE_TELEGRAM;
            $requestResult->data = Json::encode($result);
            $requestResult->index = count($result) ? Settings::get("search_index_telegram", 7) : 0;
            if (!is_null($telegramCache)) {
                $requestResult->cache_id = $telegramCache->id;
            }
            $requestResult->save();
        }

        if ($result == "[]" || !count($result)) {
            $result = [];
        } else {
            $result = [$result];
        }

        if (count(ArrayHelper::getColumn($result, "name"))) {
            $searchRequest->is_has_name = true;
        }
        if (count(ArrayHelper::getColumn($result, "photo"))) {
            $searchRequest->is_has_photo = true;
        }
        $searchRequest->save();

        return [
            "index" => count($result) ? Settings::get("search_index_telegram", 5) : 0,
            "elements" => $result
        ];
    }

    public function actionGoogleResult($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $searchRequest = SearchRequest::getDb()->cache(function () use ($id) {
            return SearchRequest::find()->where(['id' => $id])->one();
        });

        $is_vip = false;
        if ($searchRequest->user_id) {
            $user = User::find()->where(["id" => $searchRequest->user_id])->one();
            if ($user->is_vip) $is_vip = true;
        }

        $phone = $searchRequest->phone;

        $data = \Yii::$app->request->post();

        $ch = curl_init('http://127.0.0.1:1235/search/check/' . $data["uuid"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode == 200) {
            $f = fopen(Yii::getAlias("@runtime") . '/google.log', "a+");
            fwrite($f, $response . "\n\n");
            fclose($f);
            if ($response == "no") return [
                "view" => $this->renderAjax("google",
                    [
                        'items' => [],
                        'phone' => $phone,
                        'urls' => ArrayHelper::map(UrlFilter::find()->where(['type' => UrlFilter::TYPE_BANNED])->all(), 'url', 'type'),
                        "photos" => [],
                        "is_vip" => $is_vip
                    ]
                ),
                "index" => 0,
                "elements" => 0
            ];
            $result = Json::decode($response);

            if ($result["progress"] < 100) {
                return ["progress" => $result["progress"]];
            } else {
                $items = $result["result"];
                shuffle($items);
                if (count($items)) {
                    $googlePhoneCache = new ResultCache();
                    $googlePhoneCache->phone = $phone;
                    $googlePhoneCache->type_id = ResultCache::TYPE_GOOGLE_PHONE;
                    $googlePhoneCache->data = Json::encode(["items" => $items, "queries" => isset($result["queries"]) ? $result["queries"] : []]);
                    $googlePhoneCache->save();
                }

                $requestResult = RequestResult::find()->where(["request_id" => $id, "type_id" => ResultCache::TYPE_GOOGLE_PHONE])->one();
                if (is_null($requestResult)) {
                    $requestResult = new RequestResult();
                    $requestResult->request_id = $id;
                    $requestResult->type_id = ResultCache::TYPE_GOOGLE_PHONE;
                    $requestResult->data = Json::encode($result);
                    $requestResult->index = count($result) ? Settings::get("search_index_google", 7) : 0;
                    $requestResult->save();
                }

                return [
                    "view" => $this->renderAjax("google",
                        [
                            'items' => $items,
                            'phone' => $phone,
                            'urls' => ArrayHelper::map(UrlFilter::find()->where(['type' => UrlFilter::TYPE_BANNED])->all(), 'url', 'type'),
                            "photos" => isset($result["photos"]) ? $result["photos"] : [],
                            "is_vip" => $is_vip
                        ]
                    ),
                    "index" => count($items) ? Settings::get("search_index_google", 7) : 0,
                    "elements" => count($items)
                ];
            }
        }
    }

    public function actionGooglePhone($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $searchRequest = SearchRequest::getDb()->cache(function () use ($id) {
            return SearchRequest::find()->where(['id' => $id])->one();
        });


        if (is_null($searchRequest->user_id)) return [
            "index" => 0,
            "view" => $this->renderAjax("guest")
        ];

        $phone = $searchRequest->phone;

        $result = [];

        //$googlePhoneCache = ResultCache::find()->where(['phone' => $phone, 'type_id' => ResultCache::TYPE_GOOGLE_PHONE])->one();
        $googlePhoneCache = null;

        $is_vip = false;
        if ($searchRequest->user_id) {
            $user = User::find()->where(["id" => $searchRequest->user_id])->one();
            if ($user->is_vip) $is_vip = true;
        }

        if (is_null($googlePhoneCache)) {
            $data = \Yii::$app->request->post();

            $profileIds = ArrayHelper::merge(isset($data["profiles"]) ? $data["profiles"] : [], isset($data["public"]) ? $data["public"] : []);
            $profileIds = array_unique($profileIds);
            $profileId = reset($profileIds);

            $requestData = [
                "phone" => $phone,
            ];
            if ($profileId) {
                $requestData["profile_id"] = $profileId;
            }

            $ch = curl_init('http://127.0.0.1:1235/search');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, Json::encode($requestData));
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpCode == 200) { // Все ок, берем данные
                $result = Json::decode($response);
                return $result;
            }
        } else {
            $result = Json::decode($googlePhoneCache->data);
        }

        $requestResult = RequestResult::find()->where(["request_id" => $id, "type_id" => ResultCache::TYPE_GOOGLE_PHONE])->one();
        if (is_null($requestResult)) {
            $requestResult = new RequestResult();
            $requestResult->request_id = $id;
            $requestResult->type_id = ResultCache::TYPE_GOOGLE_PHONE;
            $requestResult->data = Json::encode($result);
            $requestResult->index = count($result) ? Settings::get("search_index_google", 7) : 0;
            if (!is_null($googlePhoneCache)) {
                $requestResult->cache_id = $googlePhoneCache->id;
            }
            $requestResult->save();
        }

        return [
            "view" => $this->renderAjax("google",
                [
                    "items" => isset($result["items"]) ? $result["items"] : $result,
                    "phone" => $phone,
                    'urls' => ArrayHelper::map(UrlFilter::find()->where(['type' => UrlFilter::TYPE_BANNED])->all(), 'url', 'type'),
                    "photos" => isset($result["photos"]) ? $result["photos"] : 0,
                    "is_vip" => $is_vip
                ]
            ),
            "index" => count($result) ? Settings::get("search_index_google", 7) : 0,
            "elements" => count($result)
        ];
    }

    public function actionAvinfo($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $searchRequest = SearchRequest::getDb()->cache(function () use ($id) {
            return SearchRequest::find()->where(['id' => $id])->one();
        });

        $postData = \Yii::$app->request->post();

        $phone = $searchRequest->phone;

        $block = BlockPhone::find()->where(["phone" => $searchRequest->phone, "status" => 2])->one();
        if ($block) {
            return [
                "view" => $this->renderAjax("avinfo"),
                "index" => 0
            ];
        }
        $is_mobile = ArrayHelper::getValue($postData, "is_mobile", 0);
        if ($phone == 79999999988) {
            if ($is_mobile || in_array($searchRequest->source_id, [SearchRequest::SOURCE_IOS, SearchRequest::SOURCE_ANDROID])) {
                return [
                    "elements" => [[
                        "source" => "auto.ru",
                        "credate" => "02.12.2012",
                        "marka" => "SMART",
                        "model" => "Fortwo Coupe",
                        "year" => 1999,
                        "city" => "Москва",

                        "price" => 0
                    ], [
                        "source" => "auto.ru",
                        "credate" => "01.11.2014",
                        "marka" => "BMW",
                        "model" => "5",
                        "year" => 2008,
                        "city" => "Москва",

                        "price" => 0
                    ], [
                        "source" => "auto.ru",
                        "credate" => "12.12.2016",
                        "marka" => "BMW",
                        "model" => "X6",
                        "year" => 2014,
                        "city" => "Москва",

                        "price" => 0
                    ]],//$result,
                    "phone" => $phone,
                    "resultAntiparkon" => [
                        [
                            "number" => "T123TK77",
                            "marka" => "Mini One"
                        ],
                        [
                            "number" => "P988KO777",
                            "marka" => "Nissan Teana"
                        ]
                    ],
                    "gibddResult" => [],
                    "searchRequest" => $searchRequest,
                ];
            }
            return [
                "view" => $this->renderAjax("avinfo",
                    [
                        //
                        //
                        "items" => [[
                            "source" => "auto.ru",
                            "credate" => "02.12.2012",
                            "marka" => "SMART",
                            "model" => "Fortwo Coupe",
                            "year" => 1999,
                            "city" => "Москва",

                            "price" => 0
                        ], [
                            "source" => "auto.ru",
                            "credate" => "01.11.2014",
                            "marka" => "BMW",
                            "model" => "5",
                            "year" => 2008,
                            "city" => "Москва",

                            "price" => 0
                        ], [
                            "source" => "auto.ru",
                            "credate" => "12.12.2016",
                            "marka" => "BMW",
                            "model" => "X6",
                            "year" => 2014,
                            "city" => "Москва",

                            "price" => 0
                        ]],//$result,
                        "phone" => $phone,
                        "resultAntiparkon" => [
                            [
                                "number" => "T123TK77",
                                "marka" => "Mini One"
                            ],
                            [
                                "number" => "P988KO777",
                                "marka" => "Nissan Teana"
                            ]
                        ],
                        "gibddResult" => [],
                        "searchRequest" => $searchRequest,
                    ]
                ),
                "index" => 20,
                "elements" => [["name" => "Мария Дмтриевна"]]
            ];
        }

        $result = $resultAntiparkon = [];

        if ($searchRequest->refresh && $searchRequest->is_payed) {
            $avinfoCache = null;
        } else {
            $avinfoCache = ResultCache::find()->where(['phone' => $phone, 'type_id' => ResultCache::TYPE_AVINFO_API])->orderBy(["id" => SORT_DESC])->one();
        }

        if (is_null($avinfoCache)) {
            $doAnti = true;
            if ($searchRequest->source_id == SearchRequest::SOURCE_IOS) {
                $user = $searchRequest->user;
                if ($user) {
                    /* @var $sub \app\models\UserSub */
                    $sub = UserSub::find()->where(["user_id" => $user->id])->andWhere([">=", "tm_expires", new Expression("NOW()")])->orderBy(["tm_expires" => SORT_DESC])->one();
                    if (!$sub || (strtotime($sub->tm_expires) - strtotime($sub->tm_purchase) <= 60 * 60 * 24 * 4)) {
                        $doAnti = false;
                    }
                }
            }
            if ($doAnti) {
                $ch = curl_init('http://data.av100.ru/api.ashx?key=9f256387-5260-46b6-b9dc-8abdbfb877ee&phone=' . $phone);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                if ($httpCode == 200) { // Все ок, берем данные
                    $result = Json::decode($response);
                    if (isset($result['result'])) {
                        $result = $result['result']['request'];
                        $avinfoCache = null;
                        if (count($result["auto"]) || count($result["realty"]) || count($result["other"]) || count($result["gibdd"])) {
                            $avinfoCache = new ResultCache();
                            $avinfoCache->phone = $phone;
                            $avinfoCache->type_id = ResultCache::TYPE_AVINFO_API;
                            $avinfoCache->data = Json::encode($result["auto"]);
                            $avinfoCache->save();
                        }
                        if (isset($result["auto"])) {

                            $result = $result["auto"];
                            foreach ($result as $index => $data) {
                                $images = explode(',', $data['images']);
                                $uploadedImages = [];

                                foreach ($images as $image) {
                                    try {
                                        $imageData = @file_get_contents($image);

                                        if ($imageData) {
                                            $fName = uniqid($id, true) . ".jpg";
                                            /*
                                            $file = new File();
                                            $file->uuid = uniqid($id, true);
                                            $file->type = 'image/jpeg';

                                            if (!$file->save()) continue;
                                            */

                                            $fh = fopen(Yii::$app->params['files'] . '/' . $fName, 'a+');
                                            fwrite($fh, $imageData);
                                            fclose($fh);

                                            $fh_res = fopen(Yii::$app->params['files'] . '/' . $fName, 'r');

                                            //$curl = curl_init("http://storage.apinomer.com/".$file->uuid);
                                            //$curl = curl_init("https://u158288:N46HNp0xUrzVCgSW@u158288.your-storagebox.de/nomer.io/".$file->uuid);
                                            $curl = curl_init("http://qq.apinomer.com/upload/cars/" . $fName);
                                            curl_setopt($curl, CURLOPT_PUT, true);
                                            curl_setopt($curl, CURLOPT_BINARYTRANSFER, 1);
                                            curl_setopt($curl, CURLOPT_INFILE, $fh_res);
                                            curl_setopt($curl, CURLOPT_INFILESIZE, filesize(Yii::$app->params['files'] . '/' . $fName));
                                            curl_exec($curl);
                                            curl_close($curl);

                                            fclose($fh_res);


                                            unlink(Yii::$app->params['files'] . '/' . $fName);

                                            $uploadedImages[] = $fName;
                                        }

                                    } catch (\Exception $e) {
                                        $f = fopen(\Yii::getAlias("@runtime") . '/cars.log', "a+");
                                        fwrite($f, $e->getMessage() . "\n\n");
                                        fclose($f);
                                    }
                                }

                                $result[$index]['images'] = implode(',', $uploadedImages);
                            }
                            if ($avinfoCache) {
                                $avinfoCache->data = Json::encode($result);
                                $avinfoCache->save();
                            }
                        } else {
                            $result = [];
                        }
                    } else {
                        $result = [];
                    }

                }
            }

        } else {
            $result = Json::decode($avinfoCache->data);
        }

        if (isset($result["auto"])) $result = $result["auto"];

        $requestResult = RequestResult::find()->where(["request_id" => $id, "type_id" => ResultCache::TYPE_AVINFO_API])->one();
        if (is_null($requestResult)) {
            $requestResult = new RequestResult();
            $requestResult->request_id = $id;
            $requestResult->type_id = ResultCache::TYPE_AVINFO_API;
            $requestResult->data = Json::encode($result);
            $requestResult->index = count($result) ? Settings::get("search_index_avinfo", 15) : 0;
            if (!is_null($avinfoCache)) {
                $requestResult->cache_id = $avinfoCache->id;
            }
            $requestResult->save();
        }

        // Антипаркон
        if ($searchRequest->refresh && $searchRequest->is_payed) {
            $antiparkonCache = null;
        } else {
            $antiparkonCache = ResultCache::find()->where(['phone' => $phone, 'type_id' => ResultCache::TYPE_ANTIPARKON])->one();
        }

        if (is_null($antiparkonCache)) {
            $doAnti = true;
            if ($searchRequest->source_id == SearchRequest::SOURCE_IOS) {
                $user = $searchRequest->user;
                if ($user) {
                    /* @var $sub \app\models\UserSub */
                    $sub = UserSub::find()->where(["user_id" => $user->id])->andWhere([">=", "tm_expires", new Expression("NOW()")])->orderBy(["tm_expires" => SORT_DESC])->one();
                    if (!$sub || (strtotime($sub->tm_expires) - strtotime($sub->tm_purchase) <= 60 * 60 * 24 * 4)) {
                        $doAnti = false;
                    }
                }
            }
            if ($doAnti) {
                $ch = curl_init('http://api.antiparkon.info/APIv1/phones?phone=' . $phone . '&token=hC7aoiBMvn0Z1XJOFWVx0dWE6habkMaN');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($httpCode == 200) { // Все ок, берем данные
                    $resultAntiparkon = Json::decode($response);
                    $resultAntiparkon = $resultAntiparkon["result"];
                    if (count($resultAntiparkon)) {
                        $antiparkonCache = new ResultCache();
                        $antiparkonCache->phone = $phone;
                        $antiparkonCache->type_id = ResultCache::TYPE_ANTIPARKON;
                        $antiparkonCache->data = Json::encode($resultAntiparkon);
                        $antiparkonCache->save();
                    }
                }
            }


        } else {
            $resultAntiparkon = Json::decode($antiparkonCache->data);
        }

        $requestResult = RequestResult::find()->where(["request_id" => $id, "type_id" => ResultCache::TYPE_ANTIPARKON])->one();
        if (is_null($requestResult)) {
            $requestResult = new RequestResult();
            $requestResult->request_id = $id;
            $requestResult->type_id = ResultCache::TYPE_ANTIPARKON;
            $requestResult->data = Json::encode($resultAntiparkon);
            $requestResult->index = count($resultAntiparkon) ? Settings::get("search_index_antiparkon", 5) : 0;
            if (!is_null($antiparkonCache)) {
                $requestResult->cache_id = $antiparkonCache->id;
            }
            $requestResult->save();
        }

        $elements = [];
        if ($phone != "79191030103") {
            foreach ($resultAntiparkon as $r) {
                $elements[] = ["name" => $r["name"]];
            }
        }

        $gibddResult = [];

        // Антипаркон
        if ($searchRequest->refresh && $searchRequest->is_payed) {
            $gibddCache = null;
        } else {
            $gibddCache = ResultCache::find()->where(['phone' => $phone, 'type_id' => ResultCache::TYPE_GIBDD])->one();
        }


        if (is_null($gibddCache)) {
            $gibdd = Gibdd::find()->where(["phone" => $phone])->all();
            foreach ($gibdd as $g) {
                $gibddResult[] = [
                    "model" => $g["model"],
                    "number" => $g["number"],
                    "year" => $g["year"],
                    "name" => $g["firstname"] . ' ' . $g["middlename"] . ' ' . mb_substr($g["lastname"], 0, 1)
                ];
                //$elements[] = ["name" => $g["firstname"].' '.$g["middlename"]]." ".mb_substr($g["lastname"], 0, 1);
            }
            if (count($gibddResult)) {
                $gibddCache = new ResultCache();
                $gibddCache->phone = $phone;
                $gibddCache->type_id = ResultCache::TYPE_GIBDD;
                $gibddCache->data = Json::encode($gibddResult);
                $gibddCache->save();
            }
        } else {
            $gibddResult = Json::decode($gibddCache->data);
        }

        /*
        if ($phone != "79191030103") {
            foreach ($gibddResult as $g) {
                $elements[] = ["name" => $g["name"]];
            }
        } else {
            $gibddResult = [];
        }
        */

        $requestResult = RequestResult::find()->where(["request_id" => $id, "type_id" => ResultCache::TYPE_GIBDD])->one();
        if (is_null($requestResult)) {
            $requestResult = new RequestResult();
            $requestResult->request_id = $id;
            $requestResult->type_id = ResultCache::TYPE_GIBDD;
            $requestResult->data = Json::encode($gibddResult);
            $requestResult->index = count($gibddResult) ? Settings::get("search_index_gibdd", 5) : 0;
            if (!is_null($gibddCache)) {
                $requestResult->cache_id = $gibddCache->id;
            }
            $requestResult->save();
        }

        $elements = ArrayHelper::merge($elements, $result);

        ksort($elements);


        if ($is_mobile) {
            foreach ($result as $i => $r) {
                $base64images = [];
                if (isset($r["images"])) {
                    $images = explode(",", $r["images"]);
                    foreach ($images as $image) {
                        $ii = false;
                        if (preg_match('/http/', $image)) {
                            $ii = @file_get_contents($image);
                        } else {
                            $ii = @file_get_contents(Yii::$app->params['files'] . '/' . $image);
                        }
                        if ($ii) {
                            $base64images[] = base64_encode($ii);
                        }
                    }
                }
                $result[$i]["images"] = $base64images;

            }

            if ($searchRequest->source_id == SearchRequest::SOURCE_IOS) {
                foreach ($gibddResult as $g) {
                    $elements[] = $g;
                }
            }

            return [
                "elements" => $elements,
                "avinfo" => $result,
                "antiparkon" => $resultAntiparkon,
                "gibdd" => $gibddResult,
                "index" =>
                    (count($result) ? Settings::get("search_index_avinfo", 15) : 0) +
                    (count($resultAntiparkon) ? Settings::get("search_index_antiparkon", 5) : 0) +
                    (count($gibddResult) ? Settings::get("search_index_gibdd", 5) : 0)
            ];
        }

        return [
            "view" => $this->renderAjax("avinfo",
                [
                    "items" => $result,
                    "phone" => $phone,
                    "resultAntiparkon" => $resultAntiparkon,
                    "gibddResult" => $gibddResult,
                    "searchRequest" => $searchRequest,
                ]
            ),
            "index" =>
                (count($result) ? Settings::get("search_index_avinfo", 15) : 0) +
                (count($resultAntiparkon) ? Settings::get("search_index_antiparkon", 5) : 0) +
                (count($gibddResult) ? Settings::get("search_index_gibdd", 5) : 0)
            ,
            "elements" => $elements
        ];
    }

    function avitoSort($a, $b)
    {
        $tm1 = strtotime(ArrayHelper::getValue($a, "time"));
        $tm2 = strtotime(ArrayHelper::getValue($b, "time"));

        if ($tm1 > $tm2) return 1;
        else return -1;
    }

    public function actionAvito($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $searchRequest = SearchRequest::getDb()->cache(function () use ($id) {
            return SearchRequest::find()->where(['id' => $id])->one();
        });

        $postData = \Yii::$app->request->post();
        $is_cache = ArrayHelper::getValue($postData, "is_cache", 0);

        $phone = $searchRequest->phone;

        $limit = 100;
        /*
        if($searchRequest->source_id == SearchRequest::SOURCE_IOS) {
            $user = $searchRequest->user;
            if($user) {
                /* @var $sub \app\models\UserSub *
                $sub = UserSub::find()->where(["user_id" => $user->id])->andWhere([">=", "tm_expires", new Expression("NOW()")])->orderBy(["tm_expires" => SORT_DESC])->one();
                if(!$sub || (strtotime($sub->tm_expires) - strtotime($sub->tm_purchase) <= 60 * 60 * 24 * 4)) {
                    $limit = 1;
                }
            }
        }
        */

        $resultAll = [];

        if ($searchRequest->refresh && $searchRequest->is_payed && !$is_cache) {
            $avitoCache = null;
        } else {
            $avitoCache = ResultCache::find()->where(['phone' => $phone, 'type_id' => ResultCache::TYPE_AVITO])->orderBy(["id" => SORT_DESC])->one();
        }

        if (is_null($avitoCache)) {
            $phones = \Yii::$app->request->post("phones");
            if ($phone == "79999999988") {
                $phones = [79610020300, 79162835963];
            }
            if (is_array($phones)) {
                $tphones = array_slice($phones, 0, $limit);
                foreach ($tphones as $_phone) {
                    $ch = curl_init('http://rest-app.net/api/ads?login=git@anyget.ru&token=a7548861db147975e7b3ad65c09c6398&phone=' . preg_replace('/^7/', '8', $_phone) . '&category_id=0');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                    if ($httpCode == 200) { // Все ок, берем данные
                        $result = Json::decode($response);
                        $result = $result['data'];

                        foreach ($result as $index => $data) {
                            $images = explode(',', $data['images']);
                            $uploadedImages = $images;
                        }
                        $resultAll = ArrayHelper::merge($resultAll, $result);
                    }
                }
            }


            uasort($resultAll, [$this, "avitoSort"]);

            if (count($resultAll)) {
                $avitoCache = new ResultCache();
                $avitoCache->phone = $phone;
                $avitoCache->type_id = ResultCache::TYPE_AVITO;
                $avitoCache->data = Json::encode($resultAll);
                $avitoCache->save();
            }
        } else {
            $resultAll = Json::decode($avitoCache->data);
        }

        $requestResult = RequestResult::find()->where(["request_id" => $id, "type_id" => ResultCache::TYPE_AVITO])->one();
        if (is_null($requestResult)) {
            $requestResult = new RequestResult();
            $requestResult->request_id = $id;
            $requestResult->type_id = ResultCache::TYPE_AVITO;
            $requestResult->data = Json::encode($resultAll);
            $requestResult->index = count($resultAll) ? Settings::get("search_index_avito", 15) : 0;
            if (!is_null($avitoCache)) {
                $requestResult->cache_id = $avitoCache->id;
            }
            $requestResult->save();
        }

        if (count(ArrayHelper::getColumn($resultAll, "name"))) {
            $searchRequest->is_has_name = true;
        }
        $searchRequest->save();

        if (is_null($searchRequest->user_id)) return [
            "index" => count($resultAll) ? Settings::get("search_index_avito", 15) : 0,
            "view" => count($resultAll) ? $this->renderAjax("avito_guest", ["result" => $resultAll, 'phone' => $phone]) : $this->renderAjax('guest'),
            "elements" => $resultAll
        ];

        $elements = [];
        $names = ArrayHelper::getColumn($resultAll, "name");
        $names = array_unique($names);
        foreach ($names as $name) {
            $elements[] = ["name" => $name];
        }

        $is_mobile = ArrayHelper::getValue($postData, "is_mobile", 0);
        if ($is_mobile || in_array($searchRequest->source_id, [SearchRequest::SOURCE_ANDROID, SearchRequest::SOURCE_IOS])) {
            foreach ($resultAll as $i => $r) {
                $base64images = [];
                if (isset($r["images"])) {
                    if ($searchRequest->source_id == SearchRequest::SOURCE_IOS && count($resultAll) <= 5) {
                        $images = explode(",", $r["images"]);
                        foreach ($images as $image) {
                            if (trim($image) == "") continue;
                            $fimage = @file_get_contents($image);
                            if ($fimage) {
                                $base64images[] = base64_encode($fimage);
                            }
                        }
                    } else {
                        $base64images = explode(",", $r["images"]);
                    }
                    /*
                    foreach ($images as $image) {
                        if(trim($image) == "") continue;
                        $fimage = @file_get_contents('http://storage.aprokat.com/nomerio/' . $image);
                        if($fimage) {
                            $base64images[] = base64_encode($fimage);
                        }
                    }
                    */
                }
                $resultAll[$i]["images"] = $base64images;

            }
            sort($resultAll);
            return [
                "index" => count($resultAll) ? Settings::get("search_index_avito", 15) : 0,
                "elements" => $resultAll
            ];
        }

        return [
            "view" => $this->renderAjax("avito", [
                "result" => $resultAll,
                'phone' => $phone,
                'searchRequest' => $searchRequest
            ]),
            "elements" => $elements
        ];
    }

    public function actionIndex()
    {
        $phone = \Yii::$app->request->post("phone");

        $phone = preg_replace("/[^0-9]/", "", $phone);
        if (preg_match("/8(\d{10})/u", $phone)) {
            $phone{0} = 7;
        } elseif (preg_match("/^(\d{10})$/u", $phone)) {
            $phone = "7" . $phone;
        }

        /*
        $searchRequest = new SearchRequest();
        $searchRequest->phone = $phone;
        $searchRequest->tm = date("Y-m-d H:i:s");
        $searchRequest->ip = \Yii::$app->request->getUserIP();
        $searchRequest->ua = \Yii::$app->request->getUserAgent();
        $searchRequest->source_id = SearchRequest::SOURCE_WEB;

        if (!\Yii::$app->user->isGuest) {
            $searchRequest->user_id = \Yii::$app->user->id;
        }

        $searchRequest->save();

        \Yii::$app->session->set("lastSearchId", $searchRequest->id);
        \Yii::$app->session->set("lastSearchPhone", $searchRequest->phone);
        */

        return $this->redirect(["result/index", "phone" => preg_replace("/^7/", "8", $phone)]);
    }

    function actionResult($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $searchRequest = SearchRequest::getDb()->cache(function () use ($id) {
            return SearchRequest::find()->where(['id' => $id])->one();
        });

        \Yii::$app->cache->delete("phone-" . $searchRequest->phone);

        $isGuest = true;

        if ($searchRequest->user_id) $isGuest = false;

        $operator = ResultCache::find()->where(["phone" => $searchRequest->phone, "type_id" => ResultCache::TYPE_OPERATOR])->one();
        if ($operator) {
            $operator = Json::decode($operator->data);
        }

        $is_cache = false;

        $cache = ResultCache::find()->where(['phone' => $searchRequest->phone])->andWhere([">", "tm", date("Y-m-d H:i:s", strtotime("-1 month"))])->all();
        if (count($cache) && !$searchRequest->refresh) {
            $is_cache = true;
        }

        $vk = RequestResult::find()->where(["request_id" => $searchRequest->id, "type_id" => ResultCache::TYPE_VK_2012])->one();
        $vkOpen = RequestResult::find()->where(["request_id" => $searchRequest->id, "type_id" => ResultCache::TYPE_VK_OPEN])->one();
        $fb = RequestResult::find()->where(["request_id" => $searchRequest->id, "type_id" => ResultCache::TYPE_FACEBOOK])->one();
        $avito = RequestResult::find()->where(["request_id" => $searchRequest->id, "type_id" => ResultCache::TYPE_AVITO])->one();
        $avinfo = RequestResult::find()->where(["request_id" => $searchRequest->id, "type_id" => ResultCache::TYPE_AVINFO_API])->one();
        $antiparkon = RequestResult::find()->where(["request_id" => $searchRequest->id, "type_id" => ResultCache::TYPE_ANTIPARKON])->one();

        $result2012 = [];

        if (
            !ArrayHelper::getValue($vk, "index", false) &&
            !ArrayHelper::getValue($vkOpen, "index", false) &&
            !ArrayHelper::getValue($fb, "index", false) &&
            !ArrayHelper::getValue($avito, "index", false)
        ) {
            $data = \Yii::$app->request->post();
            if (isset($data["valid"]) && is_array($data["valid"])) foreach ($data["valid"] as $profile_id) {
                $socData = @file_get_contents("https://api.vk.com/method/users.get?user_ids=" . $profile_id . "&fields=photo_id,photo_max,photo_max_orig&lang=ru&access_token=d585cf50d585cf50d585cf5077d5d94150dd585d585cf508cbd309b41310c9fdc6c13d7&v=5.62");
                if ($socData) {
                    $socData = Json::decode($socData);
                    $socData = ArrayHelper::getValue($socData, ["response", 0], null);
                    if ($socData) {
                        $names = [$socData["first_name"], $socData["last_name"]];
                        $item = [
                            "id" => $profile_id,
                            "name" => join(" ", $names),
                            "link" => "https://vk.com/id" . $profile_id
                        ];

                        if (isset($socData["photo_id"])) {
                            $photoData = @file_get_contents("https://api.vk.com/method/photos.getById?photos=" . ArrayHelper::getValue($socData, "photo_id") . "&lang=ru&access_token=d585cf50d585cf50d585cf5077d5d94150dd585d585cf508cbd309b41310c9fdc6c13d7&v=5.60");
                            if ($photoData) {
                                $photoData = Json::decode($photoData);
                                $photoData = ArrayHelper::getValue($photoData, ["response", 0], false);
                                if ($photoData) {
                                    $pUrl = ArrayHelper::getValue($photoData, "photo_2560", false);
                                    if (!$pUrl) $pUrl = ArrayHelper::getValue($photoData, "photo_1280", false);
                                    if (!$pUrl) $pUrl = ArrayHelper::getValue($photoData, "photo_130", false);
                                    $big = @file_get_contents($pUrl);
                                    if ($big) {
                                        $item["photo"] = base64_encode($big);
                                    }
                                }
                            }
                        } else {
                            $big = @file_get_contents(ArrayHelper::getValue($socData, "photo_max_orig"));
                            if ($big) {
                                $item["photo"] = base64_encode($big);
                            }
                        }
                        if (!$isGuest && $searchRequest->user->is_admin) {
                            $vkRaw = \app\models\VkRaw::find()->where(["id" => $profile_id])->one();
                            if ($vkRaw) {
                                $item["raw"] = $vkRaw->data;
                            }
                        }
                        //if(isset($data["raw"])) $item["raw"] = reset($data["raw"]);

                        $result2012[$profile_id] = $item;
                    }

                }
            }
            if ($result2012) {
                $vkCache = new ResultCache();
                $vkCache->phone = $searchRequest->phone;
                $vkCache->type_id = ResultCache::TYPE_VK_2012;
                $vkCache->data = Json::encode($result2012);
                $vkCache->save();

                $requestResult = RequestResult::find()->where(["request_id" => $id, "type_id" => ResultCache::TYPE_VK_2012])->one();
                if (is_null($requestResult)) {
                    $requestResult = new RequestResult();
                }
                $requestResult->request_id = $id;
                $requestResult->type_id = ResultCache::TYPE_VK_2012;
                $requestResult->data = Json::encode($result2012);
                $requestResult->index = count($result2012) ? Settings::get("search_index_vk", 15) : 0;
                if (!is_null($vkCache)) {
                    $requestResult->cache_id = $vkCache->id;
                }
                $requestResult->save();
            }

            $vk = RequestResult::find()->where(["request_id" => $searchRequest->id, "type_id" => ResultCache::TYPE_VK_2012])->one();
        }

        $vkVip = false;
        if (!$isGuest) {
            if ($searchRequest->user->is_vip) {
                $vkVipRow = RequestResult::find()->where(["request_id" => $searchRequest->id, "type_id" => ResultCache::TYPE_VK])->one();
                if ($vkVipRow && $vkVipRow->index > 0) {
                    $vkVip = true;
                }
            }
        }

        if (
            !ArrayHelper::getValue($vk, "index", false) &&
            !ArrayHelper::getValue($vkOpen, "index", false) &&
            !ArrayHelper::getValue($fb, "index", false) &&
            !ArrayHelper::getValue($avito, "index", false) &&
            !ArrayHelper::getValue($avinfo, "index", false) &&
            !ArrayHelper::getValue($antiparkon, "index", false) &&
            !$vkVip
        ) {
            if ($searchRequest->is_payed == 1) {
                $searchRequest->is_payed = 2;
                $searchRequest->save();
                $user = $searchRequest->user;
                if ($user->checks >= 0) {
                    $user->checks += 1;
                    $user->save();
                }
            }
        }

        $names = $photos = [];
        foreach ($searchRequest->results as $r) {
            $data = Json::decode($r->data);
            if ($data && is_array($data)) {
                try {
                    $names = ArrayHelper::merge($names, ArrayHelper::getColumn($data, "name"));
                    $photos = ArrayHelper::merge($photos, ArrayHelper::getColumn($data, "photo"));
                } catch (Exception $e) {
                    continue;
                }
            }
        }
        if ($names) {
            $searchRequest->is_has_name = true;
        }
        if ($photos) {
            $searchRequest->is_has_photo = true;
        }
        $searchRequest->save();

        $postData = Yii::$app->request->post();
        $isMobile = ArrayHelper::getValue($postData, "is_mobile", false);
        $isCache = ArrayHelper::getValue($postData, "is_cache", false);

        if ($isMobile) {
            $result = [
                "index" => array_sum(ArrayHelper::getColumn($searchRequest->results, "index")),
                "is_cache" => $isCache
            ];
            if ($result2012) {
                $result["vk"]["elements"] = $result2012;
            }
            return $result;
        }

        if ($searchRequest->is_payed == -1) {
            return [
                "view" => $this->renderAjax("wecan", [
                    "operator" => $operator,
                    "searchRequest" => $searchRequest,
                ])
            ];
        }

        if ($searchRequest->is_payed == 0) {
            return [
                "view" => $this->renderAjax("result", [
                    "isGuest" => $isGuest,
                    "operator" => $operator,
                    "is_cache" => $is_cache,
                    "index" => array_sum(ArrayHelper::getColumn($searchRequest->results, "index")),
                    "searchRequest" => $searchRequest,
                ])
            ];
        }

        $result = [
            "view" => $this->renderAjax("result", [
                "isGuest" => $isGuest,
                "operator" => $operator,
                "is_cache" => $is_cache,
                "index" => array_sum(ArrayHelper::getColumn($searchRequest->results, "index")),
                "searchRequest" => $searchRequest,
            ]),
            "vk" => $result2012 ? $result2012 : ""
        ];
        if ($result2012) {
            $result["vk"] = [
                "view" => $this->renderAjax("vk", [
                    "searchRequest" => $searchRequest,
                    "result" => $result2012,
                    "phone" => preg_replace("/^7/", "8", $searchRequest->phone)
                ]),
                "index" => count($result2012) ? Settings::get("search_index_vk", 15) : 0,
                "elements" => $result2012
            ];
        }

        return $result;
    }

    function actionMamba($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $searchRequest = SearchRequest::getDb()->cache(function () use ($id) {
            return SearchRequest::find()->where(['id' => $id])->one();
        });

        $phone = $searchRequest->phone;

        $result = 0;

        $mambaCache = ResultCache::find()->where(['phone' => $phone, 'type_id' => ResultCache::TYPE_MAMBA])->one();

        if (is_null($mambaCache)) {
            $post = \Yii::$app->request->post();
            if (isset($post["emails"]) && is_array($post["emails"])) foreach ($post["emails"] as $email) {
                $ch = curl_init('http://127.0.0.1:1235/mamba/' . $email);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($httpCode == 200) { // Все ок, берем данные
                    if ((int)$response == 1) {
                        $result++;
                    }
                }
            }
            $mambaCache = new ResultCache();
            $mambaCache->phone = $phone;
            $mambaCache->type_id = ResultCache::TYPE_MAMBA;
            $mambaCache->data = (string)$result;
            $mambaCache->save();
        } else {
            $result = $mambaCache->data;
        }

        $requestResult = RequestResult::find()->where(["request_id" => $id, "type_id" => ResultCache::TYPE_MAMBA])->one();
        if (is_null($requestResult)) {
            $requestResult = new RequestResult();
            $requestResult->request_id = $id;
            $requestResult->type_id = ResultCache::TYPE_MAMBA;
            $requestResult->data = (string)$result;
            $requestResult->index = $result ? Settings::get("search_index_mamba", 5) : 0;
            if (!is_null($mambaCache)) {
                $requestResult->cache_id = $mambaCache->id;
            }
            $requestResult->save();
        }

        return [
            "items" => $result,
            "view" => $this->renderAjax("dating", ["result" => $result]),
            "index" => $result ? Settings::get("search_index_mamba", 5) : 0,
            "elements" => $result
        ];
    }

    public function actionSprut($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $searchRequest = SearchRequest::getDb()->cache(function () use ($id) {
            return SearchRequest::find()->where(compact('id'))->one();
        });

        $phone = $searchRequest->phone;

        /* @var $user User */
        $user = User::find()->where(['id' => $searchRequest->user_id])->one();
        if (!$user || !ArrayHelper::getValue($user, 'is_vip', false)) throw new ForbiddenHttpException('Нет доступа');


        $result = null;

        /* @var $sprutCache ResultCache|null */
        $sprutCache = null;

        if (!$searchRequest->refresh) {
            $sprutCache = ResultCache::find()->where(['phone' => $phone, 'type_id' => ResultCache::TYPE_SPRUT])->one();
        }

        if ($sprutCache) {
            $result = $sprutCache->data;
        } else {
            $ch = curl_init('https://b.wcaller.com/getRaw/' . $phone);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $response = curl_exec($ch);
            curl_close($ch);

            if ($response) {
                $sprutCache = new ResultCache();
                $sprutCache->phone = $phone;
                $sprutCache->type_id = ResultCache::TYPE_SPRUT;
                $sprutCache->data = $response;
                $sprutCache->save();

                $result = $sprutCache->data;
            }
        }

        $requestResult = RequestResult::find()->where(["request_id" => $id, "type_id" => ResultCache::TYPE_SPRUT])->one();
        if (is_null($requestResult)) {
            $requestResult = new RequestResult();
            $requestResult->request_id = $id;
            $requestResult->type_id = ResultCache::TYPE_SPRUT;
            $requestResult->data = $result;
            $requestResult->index = $result ? Settings::get("search_index_sprut", 25) : 0;
            if ($sprutCache) {
                $requestResult->cache_id = $sprutCache->id;
            }
            $requestResult->save();
        }

        return [
            'view' => $this->renderAjax('sprut', ['items' => $result ? $result : null, 'phone' => $phone]),
            'index' => $result ? Settings::get("search_index_sprut", 25) : 0
        ];
    }

    public function actionInfo($id)
    {
        $searchRequest = SearchRequest::getDb()->cache(function () use ($id) {
            return SearchRequest::find()->where(compact('id'))->one();
        });

        $phone = $searchRequest->phone;
    }

    public function actionTruecaller($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $searchRequest = SearchRequest::getDb()->cache(function () use ($id) {
            return SearchRequest::find()->where(compact('id'))->one();
        });

        $data = \Yii::$app->request->post();
        $is_cache = ArrayHelper::getValue($data, "is_cache", 0);
        //if(is_null($searchRequest->user_id)) throw new ForbiddenHttpException("Нет доступа");

        $phone = $searchRequest->phone;
        if ($phone == "79999999988") {
            return ["elements" => [["name" => "Машка"], ["name" => "Машулька"]]];
        }

        $block = BlockPhone::find()->where(["phone" => $searchRequest->phone, "status" => 2])->one();
        if ($block && !ArrayHelper::getValue($searchRequest, ["user", "is_admin"])) {
            return [];
        }

        $result = [];

        /* @var $truecallerCache ResultCache|null */
        $truecallerCache = null;

        if (!$searchRequest->refresh && !$is_cache) {
            $truecallerCache = ResultCache::find()->where(['phone' => $phone, 'type_id' => ResultCache::TYPE_TRUECALLER])->orderBy(["id" => SORT_DESC])->one();
        }

        if ($truecallerCache) {
            $result = Json::decode($truecallerCache->data);
        } else {

            $doAnti = true;
            if ($searchRequest->source_id == SearchRequest::SOURCE_IOS) {
                $doAnti = false;
            }

            if($doAnti) {
                $phones = \Yii::$app->request->post("phones");
                if (is_array($phones)) {
                    foreach ($phones as $_phone) {
                        $curl = curl_init("https://search5-noneu.truecaller.com/v2/search?countryCode=ru&locAddr=&pageId=&q=" . substr($_phone, 1, 10) . "&type=4");
                        curl_setopt($curl, CURLOPT_USERAGENT, "Truecaller/9.0.0 (com.truesoftware.TrueCallerOther; build:11; iOS 11.2.2; device:iPhone7,2) AFNetworking");
                        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                            'Authorization: Bearer a1i031324386033TF8nyZITUu5Z8gKNZzMk9ypZ60x5wmRydypkIDmLNU4XLjO0K',
                            //'Accept-Encoding: br, gzip, deflate',
                            'Accept: application/json',
                            'Accept-Language: ru-RU;q=1'
                        ));
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        $data = curl_exec($curl);
                        curl_close($curl);

                        $data = Json::decode($data);

                        $elements = [];
                        if ($data && isset($data['data'])) {
                            foreach ($data['data'] as $profile) {
                                $element = [];

                                $name = ArrayHelper::getValue($profile, 'name');
                                if ($name) $element['name'] = $name;

                                $image = ArrayHelper::getValue($profile, 'image');
                                if ($image) $element['photo'] = base64_encode(file_get_contents($image));

                                if ($element) $elements[] = $element;
                            }
                        }

                        $result = ArrayHelper::merge($result, $elements);
                    }
                }

                if ($result) {
                    $truecallerCache = new ResultCache();
                    $truecallerCache->phone = $phone;
                    $truecallerCache->type_id = ResultCache::TYPE_TRUECALLER;
                    $truecallerCache->data = Json::encode($result);
                    $truecallerCache->save();
                }
            }


        }

        $requestResult = RequestResult::find()->where(["request_id" => $id, "type_id" => ResultCache::TYPE_TRUECALLER])->one();
        if (is_null($requestResult)) {
            $requestResult = new RequestResult();
            $requestResult->request_id = $id;
            $requestResult->type_id = ResultCache::TYPE_TRUECALLER;
            $requestResult->data = Json::encode($result);
            $requestResult->index = $result ? Settings::get("search_index_truecaller", 10) : 0;

            if ($truecallerCache) {
                $requestResult->cache_id = $truecallerCache->id;
            }

            $requestResult->save();
        }

        if (count(ArrayHelper::getColumn($result, "name"))) {
            $searchRequest->is_has_name = true;
        }
        if (count(ArrayHelper::getColumn($result, "photo"))) {
            $searchRequest->is_has_photo = true;
        }
        $searchRequest->save();

        return [
            'elements' => $result ? $result : [],
            'index' => $result ? Settings::get("search_index_truecaller", 10) * count($result) : 0
        ];
    }

    public function actionGetcontact($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $searchRequest = SearchRequest::getDb()->cache(function () use ($id) {
            return SearchRequest::find()->where(compact('id'))->one();
        });

        $data = \Yii::$app->request->post();
        $is_cache = ArrayHelper::getValue($data, "is_cache", 0);

        $phone = $searchRequest->phone;

        $block = BlockPhone::find()->where(["phone" => $searchRequest->phone, "status" => 2])->one();
        if ($block && !ArrayHelper::getValue($searchRequest, ["user", "is_admin"])) {
            return [];
        }

        if ($phone == "79999999988") return [];


        $result = [];

        /* @var $getcontactCache ResultCache|null */
        $getcontactCache = null;

        if (!$searchRequest->refresh && !$is_cache) {
            $getcontactCache = ResultCache::find()->where(['phone' => $phone, 'type_id' => ResultCache::TYPE_GETCONTACT])->orderBy(["id" => SORT_DESC])->one();
        }

        if ($getcontactCache) {
            $result = Json::decode($getcontactCache->data);
        } else {
            $doAnti = true;
            if ($searchRequest->source_id == SearchRequest::SOURCE_IOS) {
                $user = $searchRequest->user;
                if ($user) {
                    /* @var $sub \app\models\UserSub */
                    $sub = UserSub::find()->where(["user_id" => $user->id])->andWhere([">=", "tm_expires", new Expression("NOW()")])->orderBy(["tm_expires" => SORT_DESC])->one();
                    if (!$sub || (strtotime($sub->tm_expires) - strtotime($sub->tm_purchase) <= 60 * 60 * 24 * 4)) {
                        $doAnti = false;
                    }
                }
            }
            if ($doAnti) {
                $tokens = ["4f04576e3e18ce413674be9277344f14", "e3bb3e83ad3656b02d700f0a4b5ee300"];
                $t = array_rand($tokens, 1);
                $proxy = file_get_contents("https://awmproxy.com/proxy/938457c3e0315d23ef35d2a52a6b03bf?country-only=ru&limit=1");
                //    $ch = curl_init('https://api.numbuster.com/api/person/by_phone/' . $phone . '?access_token=' . $numbuster);
                $ch = curl_init('https://getcontact.com/api/phone?msisdn=%2B' . $phone . '&token=' . $tokens[$t] . '&locale=en_RU&lang=ru_RU&source=search&check_banned=1');
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_PROXY, $proxy);
                curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
                $response = curl_exec($ch);
                $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($responseCode == 200) {
                    $getcontactCache = new ResultCache();
                    $getcontactCache->phone = $phone;
                    $getcontactCache->type_id = ResultCache::TYPE_GETCONTACT;


                    $data = Json::decode($response);

                    if (ArrayHelper::getValue($data, "status") == 1) {
                        $elements = [];

                        if ($data) {
                            $names = ArrayHelper::getValue($data, ["response", "list", 0, "other_names"]);

                            foreach ($names as $name) {
                                $elements[] = ["name" => $name];
                            }
                        }

                        $getcontactCache->data = Json::encode($elements);
                        $getcontactCache->save();

                        $result = $elements;
                    }
                }
            }
        }

        $requestResult = RequestResult::find()->where(["request_id" => $id, "type_id" => ResultCache::TYPE_GETCONTACT])->one();
        if (is_null($requestResult)) {
            $requestResult = new RequestResult();
            $requestResult->request_id = $id;
            $requestResult->type_id = ResultCache::TYPE_GETCONTACT;
            $requestResult->data = Json::encode($result);
            $requestResult->index = $result ? Settings::get("search_index_getcontact", 5) : 0;

            if ($getcontactCache) {
                $requestResult->cache_id = $getcontactCache->id;
            }

            $requestResult->save();
        }

        return [
            'elements' => $result ? $result : [],
            'index' => $result ? Settings::get("search_index_getcontact", 5) * count($result) : 0
        ];
    }

    public function actionNumbuster($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $searchRequest = SearchRequest::getDb()->cache(function () use ($id) {
            return SearchRequest::find()->where(compact('id'))->one();
        });

        //if(is_null($searchRequest->user_id)) throw new ForbiddenHttpException("Нет доступа");
        $data = \Yii::$app->request->post();
        $is_cache = ArrayHelper::getValue($data, "is_cache", 0);

        $phone = $searchRequest->phone;

        $block = BlockPhone::find()->where(["phone" => $searchRequest->phone, "status" => 2])->one();
        if ($block && !ArrayHelper::getValue($searchRequest, ["user", "is_admin"])) {
            return [];
        }

        if ($phone == "79999999988") return [];


        $result = [];

        /* @var $numbusterCache ResultCache|null */
        $numbusterCache = null;

        if (!$searchRequest->refresh && !$is_cache) {
            $numbusterCache = ResultCache::find()->where(['phone' => $phone, 'type_id' => ResultCache::TYPE_NUMBUSTER])->orderBy(["id" => SORT_DESC])->one();
        }

        if ($numbusterCache) {
            $result = Json::decode($numbusterCache->data);
        } else {
            $curl = curl_init("https://api.numbuster.com/api/v3/profiles/by_phone/" . $phone . "?access_token=19tsgp4tj1s04ooc040c48kgs44wg0go08w8o88k40w08kwkcs");
            curl_setopt($curl, CURLOPT_USERAGENT, "%D0%9A%D1%82%D0%BE%20%D0%B7%D0%B2%D0%BE%D0%BD%D0%B8%D0%BB%3F/58 CFNetwork/901.1 Darwin/17.6.0");
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                //'Accept-Encoding: br, gzip, deflate',
                'Accept: */*',
                'Accept-Language: ru',
                'Content-Type: application/x-www-form-urlencoded'
            ));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $data = curl_exec($curl);
            curl_close($curl);

            $f = fopen(\Yii::getAlias("@runtime").'/nb.log', 'a+');
            fwrite($f, $data."\n\n");
            fclose($f);
//            $data = @file_get_contents("https://api.numbuster.com/api/v3/profiles/by_phone/" . $phone . "?access_token=4wf81q3r538ko4scc4wsw4owsc4s0o48o84wokw4osco44oock");


            try {
                $data = Json::decode($data);
            } catch (Exception $e) {
                return [
                    'elements' => [],
                    'index' => 0
                ];
            }

            $numbusterCache = new ResultCache();
            $numbusterCache->phone = $phone;
            $numbusterCache->type_id = ResultCache::TYPE_NUMBUSTER;

            $elements = [];


            if ($data) {
                $element = [];

                $profiles = [
                    [
                        "firstName" => $data["firstName"],
                        "lastName" => $data["lastName"],
                        "avatar" => $data["avatar"],
                    ]
                ];

                if (isset($data['profile'])) {
                    $profiles[] = $data['profile'];
                }

                if (isset($data['averageProfile'])) {
                    $profiles[] = $data['averageProfile'];
                }

                if (isset($data['contacts']) && is_array($data['contacts']) && $data['antispy_subscription'] === false) {
                    foreach($data['contacts'] as $c) {
                        $profiles[] = $c;
                    }
                }

                foreach ($profiles as $profile) {
                    $element = [];
                    $element['name'] = '';

                    if ($profile['firstName']) {
                        $element['name'] .= $profile['firstName'] . ' ';
                    }

                    if ($profile['lastName']) {
                        $element['name'] .= $profile['lastName'];
                    }

                    if (isset($profile['avatar'])) {
                        $element['photo'] = base64_encode(file_get_contents($profile['avatar']));
                    }

                    $element['name'] = trim($element['name']);
                    if ($element['name'] == '') unset($element['name']);

                    if ($element) $elements[] = $element;
                }

                $numbusterCache->data = Json::encode($elements);
                $numbusterCache->save();

                $result = $elements;
            }
        }

        $requestResult = RequestResult::find()->where(["request_id" => $id, "type_id" => ResultCache::TYPE_NUMBUSTER])->one();
        if (is_null($requestResult)) {
            $requestResult = new RequestResult();
            $requestResult->request_id = $id;
            $requestResult->type_id = ResultCache::TYPE_NUMBUSTER;
            $requestResult->data = Json::encode($result);
            $requestResult->index = $result ? Settings::get("search_index_numbuster", 5) : 0;

            if ($numbusterCache) {
                $requestResult->cache_id = $numbusterCache->id;
            }

            $requestResult->save();
        }

        if (count(ArrayHelper::getColumn($result, "name"))) {
            $searchRequest->is_has_name = true;
        }
        if (count(ArrayHelper::getColumn($result, "photo"))) {
            $searchRequest->is_has_photo = true;
        }
        $searchRequest->save();

        return [
            'elements' => $result ? $result : [],
            'index' => $result ? Settings::get("search_index_numbuster", 5) * count($result) : 0
        ];
    }
}
