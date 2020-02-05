<?php

namespace app\controllers;

use app\components\SearchHelper;
use app\models\RequestResult;
use app\models\Settings;
use app\models\Site;
use app\models\User;
use app\models\UserContact;
use Yii;
use app\models\BlockPhone;
use app\models\ResultCache;
use app\models\SearchRequest;
use app\models\UrlFilter;
use yii\base\Exception;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class ResultController extends Controller {

    private function guest($phone, $source) {


        $searchRequest = new SearchRequest();
        $searchRequest->ip = \Yii::$app->request->userIP;
        $searchRequest->ua = \Yii::$app->request->userAgent;
        $searchRequest->phone = $phone;
        $searchRequest->tm = new Expression("NOW()");
        $searchRequest->user_id = null;
        $searchRequest->refresh = false;
        $searchRequest->source_id = $source;
        $searchRequest->site_id = $this->siteId;
        if(ArrayHelper::getValue($this->site, "type_id") == 2) {
            $q = SearchRequest::find()->where(["ip" => \Yii::$app->request->userIP])->andWhere([">=", "tm", date("Y-m-d H:i:s", strtotime("-1 day"))])->count();
            if(!$q) {
                $searchRequest->is_payed = -1;
            }
        }
        $searchRequest->save();

        $result = SearchHelper::Operator($phone);
        $requestResult = RequestResult::find()->where(["request_id" => $searchRequest->id, "type_id" => ResultCache::TYPE_OPERATOR])->one();
        if (is_null($requestResult)) {
            $requestResult = new RequestResult();
            $requestResult->request_id = $searchRequest->id;
            $requestResult->type_id = ResultCache::TYPE_OPERATOR;
            $requestResult->data = Json::encode($result);
            $requestResult->index = $result ? Settings::get("search_index_operator", 5) : 0;

            $requestResult->save();
        }

        $jobCount = `/home/nomer.io/www/yii queue/info | grep waiting | grep -o '[0-9]*'`;

        return $this->render("free", [
            'phone'         => $phone,
            'searchRequest' => $searchRequest,
            'is_cache'      => false,
            'jobCount'     => $jobCount
        ]);
    }

    private $siteId = 0;

    /* @var $site \app\models\Site */
    private $site;

    public function actionIndex($phone, $token = "") {
        $phone = preg_replace("/\D/", "", $phone);
        $phone = preg_replace("/^8/", "7", $phone);
        if(mb_strlen($phone) != 11 || !preg_match('/79(\d{9})/', $phone)) {
            \Yii::$app->session->setFlash("error", "Номер $phone указан не корректно! Мы работаем только с мобильными номерами России.");
            return $this->goHome();
        }

        Yii::$app->user->returnUrl = Yii::$app->request->url;

        /*
        $count = SearchRequest::find()->where(["ip" => \Yii::$app->request->getUserIP()])->count();
        if($count > 15 && \Yii::$app->getUser()->isGuest) {
            return $this->render('please');
        }
        */

        if($token != "" && \Yii::$app->getUser()->isGuest) {
            $user = User::findIdentityByAccessToken($token);
            if($user) {
                \Yii::$app->user->login($user, 3600 * 24 * 30);
                return $this->refresh();
            }
        }

        $source = SearchRequest::SOURCE_WEB;
        if (isset($_SERVER["is_mobile"]) && $_SERVER["is_mobile"] == 1) {
            $source = SearchRequest::SOURCE_MOBILE;
        }

        $site = Site::find()->where(["name" => $_SERVER["HTTP_HOST"]])->one();
        $this->site = $site;
        $this->siteId = ArrayHelper::getValue($site, "id", 0);
        if(ArrayHelper::getValue($site, 'is_demo', false)) {
            $operator = SearchHelper::Operator($phone);
            return $this->render('demo', [
                "phone" => $phone,
                "operator" => $operator
            ]);
        }

        if(\Yii::$app->getUser()->isGuest) {
            $block = BlockPhone::find()->where(["phone" => $phone, "site_id" => [ArrayHelper::getValue($site, 'id'), 0], "status" => [1, 2]])->one();
            if(!is_null($block) && $block->status == 1) {
                return $this->render("block", ["phone" => $phone]);
            } elseif(!is_null($block) && $block->status == 2) {
                $url = Url::to(['https://smsc.ru/sys/send.php',
                    'login'     => 'admeo',
                    'psw'       => 'admeosmsc',
                    'phones'    => $phone,
                    'mes'       => 'Ваш номер пробивали анонимно с IP: ' .\Yii::$app->request->getUserIP(),
                    'charset'   => 'utf-8',
                    'sender'    => Yii::$app->name
                ], 'https');
                @file_get_contents($url);
            }

            $countSeaches = 0;
            $freePhones = [];
            $seaches = SearchRequest::find()->where(["ip" => \Yii::$app->request->getUserIP()])->andWhere(["<>", "ip", "82.204.203.174"])->andWhere(["<>", "ip", "81.88.218.82"])->andWhere([">", "tm", date("Y-m-d H:i:s", strtotime("-7 days"))])->all();
            foreach ($seaches as $s) {
                if($s->is_has_name && $s->is_has_photo && !in_array($s->phone, $freePhones)) {
                    $countSeaches++;
                    $freePhones[] = $s->phone;
                }
                if($countSeaches == 3) break;
            }
            if($countSeaches >= 3) return $this->render("please", ["phone" => $phone]);
            return $this->guest($phone, $source);
        }

        $refresh = \Yii::$app->request->get("refresh", false);
        if($refresh == 1) $refresh = true;

        if(in_array($phone, ["79999999988", "79645552229"])) $refresh = true;

        $result = [];

        $is_cache = false;

        /* @var $user \app\models\User */
        $user = \Yii::$app->getUser()->getIdentity();

        $searchRequest = null;

        if(!$refresh) {
            $searchRequest = SearchRequest::find()->where([
                "user_id"   => \Yii::$app->getUser()->getId(),
                "phone"     => $phone,
                "is_payed" => [1, 2]

            ])->orderBy(["id" => SORT_DESC])->one();
            if($searchRequest) {
                $is_cache = true;
            }
        }

        /*
        if(!$user->is_vip && \Yii::$app->params["payModel"]) {
            if(!$user->checks && $user->balance < \Yii::$app->params["cost"]) {
                return $this->render("pay");
            }
        }
        */

        $block = BlockPhone::find()->where(["phone" => $phone, "site_id" => [ArrayHelper::getValue($site, "id", 0), 0], "status" => [1]])->one();
        if(!is_null($block) && !$user->is_vip) {
            return $this->render("block", ["phone" => $phone]);
        }

        $block = BlockPhone::find()->where(["phone" => $phone, "site_id" => [ArrayHelper::getValue($site, "id", 0), 0], "status" => [2]])->one();
        if(!is_null($block) && !$user->is_admin) {
            $url = Url::to(['https://smsc.ru/sys/send.php',
                'login' => 'admeo',
                'psw' => 'admeosmsc',
                'phones' => $phone,
                'mes' => 'Ваш номер пробивал '.$user->email.' с IP: ' . \Yii::$app->request->getUserIP(),
                'charset' => 'utf-8',
                'sender' => Yii::$app->name
            ], 'https');
            @file_get_contents($url);
        }

        if(!$searchRequest) {
            $searchRequest = new SearchRequest();
            $searchRequest->ip = \Yii::$app->request->userIP;
            $searchRequest->ua = \Yii::$app->request->userAgent;
            $searchRequest->phone = $phone;
            $searchRequest->tm = new Expression("NOW()");
            $searchRequest->user_id = $user->id;
            $searchRequest->refresh = (boolean)$refresh;
            $searchRequest->source_id = $source;
            $searchRequest->is_payed = 0;
            $searchRequest->site_id = $this->siteId;
            if(ArrayHelper::getValue($this->site, 'type_id', 1) == 2) {
                $q = SearchRequest::find()->where(["ip" => \Yii::$app->request->userIP])->andWhere([">=", "tm", date("Y-m-d H:i:s", strtotime("-1 day"))])->count();
                if(!$q) {
                    $searchRequest->is_payed = -1;
                }
            }

            if(\Yii::$app->params["payModel"]) {
                if($user->checks > 0) {
                    $user->checks--;
                    $searchRequest->is_payed = 1;
                } elseif($user->balance >= \Yii::$app->params["cost"]) {
                    $user->balance -= \Yii::$app->params["cost"];
                    $searchRequest->is_payed = 1;
                }/* elseif($user->is_vip) {
                    $searchRequest->is_payed = 3;
                }*/
                $user->save();
            }

            if(!$searchRequest->is_payed) {
                /*
                $countSeaches = 0;
                $seaches = SearchRequest::find()->where(["and", ["ip" => \Yii::$app->request->getUserIP()], ["<>", "ip", "82.204.203.174"], [">", "tm", date("Y-m-d H:i:s", strtotime("-12 hours"))]])->orWhere(["user_id" => \Yii::$app->getUser()->getId()])->all();
                foreach ($seaches as $s) {
                    $caches = RequestResult::find()->where(["request_id" => $s->id])->andWhere(["<>", "type_id", ResultCache::TYPE_SPRUT])->all();
                    $names = $photos = [];
                    foreach ($caches as $c) {
                        try {
                            $data = Json::decode($c->data);
                            if($data && is_array($data)) {
                                $names = ArrayHelper::merge($names, ArrayHelper::getColumn($data, "name"));
                                $photos = ArrayHelper::merge($photos, ArrayHelper::getColumn($data, "photo"));
                            }
                        } catch (Exception $e) {
                            continue;
                        }
                    }
                    $names = array_filter($names);
                    $photos = array_filter($photos);
                    if($names || $photos) {
                        $countSeaches++;
                    }
                    if($countSeaches == 3) break;
                }
                if($countSeaches >= 3) return $this->render("please");
                */
                $countSeaches = 0;
                $freePhones = [];
                $seaches = SearchRequest::find()->where(["and", ["ip" => \Yii::$app->request->getUserIP()], ["<>", "ip", "82.204.203.174"], [">", "tm", date("Y-m-d H:i:s", strtotime("-7 days"))]])->andWhere(["user_id" => \Yii::$app->getUser()->getId()])->all();
                foreach ($seaches as $s) {
                    if($s->is_has_name && $s->is_has_photo && !in_array($s->phone, $freePhones)) {
                        $countSeaches++;
                        $freePhones[] = $s->phone;
                    }
                    if($countSeaches == 3) break;
                }
                if($countSeaches >= 3) return $this->render("please", ["phone" => $phone]);
            }

            $searchRequest->save();
        }

        $checkBanPhone = SearchRequest::find()->where(["requests.phone" => $phone])->joinWith(["user" => function(\yii\db\ActiveQuery $q) {
            $q->andWhere(["status" => 0]);
        }])->andWhere(["<>", "user_id", $user->id])->all();

        if(count($checkBanPhone) && $user->is_test) {
            $user->status = 0;
            $user->ban = User::BAN_PHONE;
            $user->save();
        }

        /*
        if($user->status == 0 && !$user->is_vip) {
            if($user->phone) {
                $url = "https://smsc.ru/sys/send.php?login=admeo&psw=admeosmsc&phones=$phone&mes=".urlencode("Ваш номер пытался пробить владелец телефона +".$user->phone." на сайте ".\Yii::$app->name)."&charset=utf-8&sender=".\Yii::$app->name;
                file_get_contents($url);
            } else {
                $url = "https://smsc.ru/sys/send.php?login=admeo&psw=admeosmsc&phones=$phone&mes=".urlencode("Ваш номер пытался пробить владелец e-mail адреса ".$user->email." на сайте ".\Yii::$app->name)."&charset=utf-8&sender=".\Yii::$app->name;
                file_get_contents($url);
            }
            return $this->render("ban", ["phone" => $phone]);
        };
        */



        if (!Yii::$app->getUser()->isGuest) {
            UserContact::updateAll(['last_check' => $searchRequest->tm], ['phone' => $searchRequest->phone, 'user_id' => $searchRequest->user_id]);
        }

        $log = [];
        if($user->is_admin) {
            $log = SearchRequest::find()->where(["phone" => $phone])->with("user")->asArray()->orderBy(["id" => SORT_DESC])->all();
        }

        if($is_cache) {
            return $this->render("cache", [
                'searchRequest' => $searchRequest,
                'log' => $log
            ]);
        }

        if(!$searchRequest->is_payed) {
            $result = SearchHelper::Operator($phone);
            $requestResult = RequestResult::find()->where(["request_id" => $searchRequest->id, "type_id" => ResultCache::TYPE_OPERATOR])->one();
            if(is_null($requestResult)) {
                $requestResult = new RequestResult();
                $requestResult->request_id = $searchRequest->id;
                $requestResult->type_id = ResultCache::TYPE_OPERATOR;
                $requestResult->data = Json::encode($result);
                $requestResult->index = $result?Settings::get("search_index_operator", 5):0;

                $requestResult->save();
            }

            return $this->render("free", [
                'searchRequest' => $searchRequest
            ]);
        }



        return $this->render("index", [
            'searchRequest' => $searchRequest,
            'log' => $log
        ]);
    }

    public function actionVk($phone) {
        $vkCache = ResultCache::find()->where(['phone' => preg_replace('/^8/', '7', $phone), 'type_id' => ResultCache::TYPE_VK])->one();
        if(is_null($vkCache)) {
            throw new NotFoundHttpException("Страница не найдена");
        }

        $vkCacheData = Json::decode($vkCache->data);

        return $this->render("vk", [
            "phone"  => $phone,
            "result" => $vkCacheData["result2012"]
        ]);
    }

    public function actionGoogle($phone) {
        if(\Yii::$app->getUser()->isGuest) {
            throw new ForbiddenHttpException("Нет доступа");
        }
        $googleCache = ResultCache::find()->where(['phone' => preg_replace('/^8/', '7', $phone), 'type_id' => ResultCache::TYPE_GOOGLE_PHONE])->orderBy(["id" => SORT_DESC])->one();
        if(is_null($googleCache)) {
            throw new NotFoundHttpException("Страница не найдена");
        }

        $urls = ArrayHelper::map(UrlFilter::find()->all(), "url", "type");

        return $this->render("google", [
            "phone"  => $phone,
            "result" => Json::decode($googleCache->data),
            "urls" => $urls
        ]);
    }

    public function actionAvinfo($phone) {
        if(\Yii::$app->getUser()->isGuest) {
            throw new ForbiddenHttpException("Нет доступа");
        }
        $avinfoCache = ResultCache::find()->where(['phone' => preg_replace('/^8/', '7', $phone), 'type_id' => ResultCache::TYPE_AVINFO])->one();
        $antiparkonCache = ResultCache::find()->where(['phone' => preg_replace('/^8/', '7', $phone), 'type_id' => ResultCache::TYPE_ANTIPARKON])->one();

        if(is_null($avinfoCache) && is_null($antiparkonCache)) {
            throw new NotFoundHttpException("Страница не найдена");
        }

        return $this->render("avinfo", [
            "phone"  => $phone,
            "result" => $avinfoCache?Json::decode($avinfoCache->data):[],
            'resultAntiparkon' => $antiparkonCache?Json::decode($antiparkonCache->data):[],
        ]);
    }

    public function actionLog($phone) {
        if(\Yii::$app->getUser()->isGuest || !\Yii::$app->getUser()->getIdentity()->is_admin) {
            throw new ForbiddenHttpException("Нет доступа");
        }

        $log = SearchRequest::find()->where(["phone" => $phone])->with("user")->asArray()->orderBy(["id" => SORT_DESC])->all();

        return $this->render('log', [
            'log'   => $log,
            'phone' => $phone
        ]);
    }


    public function actionAvito($phone, $id = null) {
        if(\Yii::$app->getUser()->isGuest) {
            throw new ForbiddenHttpException("Нет доступа");
        }

        $avitoCache = ResultCache::find()->where(['phone' => preg_replace('/^8/', '7', $phone), 'type_id' => ResultCache::TYPE_AVITO])->orderBy(["id" => SORT_DESC])->one();
        if(is_null($avitoCache)) {
            throw new NotFoundHttpException("Страница не найдена");
        }

        if($id) {
            return $this->render("avito_item", [
                "id"        => $id,
                "phone"     => $phone,
                "result"    => Json::decode($avitoCache->data)
            ]);
        }

        return $this->render("avito", [
            "phone"  => $phone,
            "result" => Json::decode($avitoCache->data)
        ]);
    }

    public function actionScorista($phone)
    {
        if(\Yii::$app->getUser()->isGuest) {
            throw new ForbiddenHttpException("Нет доступа");
        }
        if (!ArrayHelper::getValue(Yii::$app->getUser()->getIdentity(), 'is_vip', false)) throw new ForbiddenHttpException('Нет доступа');
        /* @var $sprutCache ResultCache */

        $searchRequest = SearchRequest::find()->where(["phone" => preg_replace('/^8/', '7', $phone)])->orderBy(["id" => SORT_DESC])->limit(1)->one();
        $result = RequestResult::find()->where(["request_id" => $searchRequest->id, "type_id" => ResultCache::TYPE_SCORISTA])->one();

        if (!$result) throw new NotFoundHttpException('Страница не найдена');
        return $this->render('sprut', ['result' => $result->data, 'phone' => $phone]);
    }
}
