<?php
namespace app\controllers;

use app\models\BlockPhone;
use app\models\ResultCache;
use app\models\SearchRequest;
use app\models\UrlFilter;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class FrameController extends Controller {

    public $layout = 'frame';

    public function actionIndex($phone) {
        $phone = preg_replace("/^8/", "7", $phone);

        $refresh = \Yii::$app->request->get("refresh", false);

        $result = [];

        $block = BlockPhone::find()->where(["phone" => $phone, "status" => 1])->one();
        if(!is_null($block)) {
            return $this->render("block", ["phone" => $phone]);
        }

        $cache = ResultCache::find()->where(['phone' => $phone])->andWhere([">", "tm", date("Y-m-d H:i:s", strtotime("-1 month"))])->all();
        if(count($cache) && !$refresh) {
            $result["cache"] = true;
        }

        if (preg_match("/79(\d{9})/", $phone)) {
            $operatorCache = ResultCache::find()->where(["phone" => $phone, "type_id" => ResultCache::TYPE_OPERATOR])->one();
            if(is_null($operatorCache)) {
                $operator = @file_get_contents("https://moscow.megafon.ru/api/mfn/info?msisdn=" . $phone);
                if ($operator) {
                    $operator = Json::decode($operator);

                    if (!is_null($operator) && !isset($operator["error"])) {
                        $result["mobile"]["operator"] = $operator["operator"];
                        $result["mobile"]["region"] = $operator["region"];
                        $operatorCache = new ResultCache();
                        $operatorCache->phone = $phone;
                        $operatorCache->type_id = ResultCache::TYPE_OPERATOR;
                        $operatorCache->data = Json::encode($result["mobile"]);
                        $operatorCache->save();
                    }
                }
            } else {
                $result["mobile"] = Json::decode($operatorCache->data);
            }
        }

        $lastId = \Yii::$app->session->get("lastSearchId", null);
        $lastPhone = \Yii::$app->session->get("lastSearchPhone");
        if($phone !== $lastPhone) {
            $searchRequest = new SearchRequest();
            $searchRequest->ip = \Yii::$app->request->userIP;
            $searchRequest->ua = \Yii::$app->request->userAgent;
            $searchRequest->phone = $phone;
            $searchRequest->tm = new Expression("NOW()");
            $searchRequest->user_id = \Yii::$app->getUser()->isGuest?null:\Yii::$app->getUser()->getId();
            $searchRequest->refresh = (boolean)$refresh;
            $searchRequest->save();
            $lastId = $searchRequest->id;
        }

        $log = SearchRequest::find()->where(["phone" => $phone])->andWhere(["<>", "id", $lastId])->orderBy(["id" => SORT_DESC])->all();


        return $this->render("index", [
            'id'        => $lastId,
            'phone'     => $phone,
            'result'    => $result,
            'log'       => $log
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
        $googleCache = ResultCache::find()->where(['phone' => preg_replace('/^8/', '7', $phone), 'type_id' => ResultCache::TYPE_GOOGLE_PHONE])->one();
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
        $avinfoCache = ResultCache::find()->where(['phone' => preg_replace('/^8/', '7', $phone), 'type_id' => ResultCache::TYPE_AVINFO])->one();
        if(is_null($avinfoCache)) {
            throw new NotFoundHttpException("Страница не найдена");
        }

        return $this->render("avinfo", [
            "phone"  => $phone,
            "result" => Json::decode($avinfoCache->data)
        ]);
    }

    public function actionAvito($phone, $id = null) {
        $avitoCache = ResultCache::find()->where(['phone' => preg_replace('/^8/', '7', $phone), 'type_id' => ResultCache::TYPE_AVITO])->one();
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

}