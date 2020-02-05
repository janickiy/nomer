<?php
namespace app\controllers;

use app\models\RequestResult;
use app\models\ResultCache;
use app\models\SearchRequest;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Response;

class MController extends Controller {

    public function actionFacebook($id) {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $result = RequestResult::find()->where(["request_id" => $id, "type_id" => ResultCache::TYPE_FACEBOOK])->one();
        if(is_null($result)) return [];

        $data = Json::decode($result->data);
        sort($data);

        return $data;
    }

    public function actionVk($id) {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $result = RequestResult::find()->where(["request_id" => $id, "type_id" => ResultCache::TYPE_VK])->one();
        if(is_null($result)) return [];

        $data = Json::decode($result->data);
        sort($data);

        return $data;
    }

    public function actionAvito($id) {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $result = RequestResult::find()->where(["request_id" => $id, "type_id" => ResultCache::TYPE_AVITO])->one();
        if(is_null($result)) return [];

        $data = Json::decode($result->data);
        //sort($data);

        return $data;
    }
}