<?php

namespace app\commands;

use app\models\Proxy;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class TasksController extends Controller
{
    public function actionUpdateProxies()
    {
        $proxies = explode("\n", trim(file_get_contents('http://awmproxy.com/777proxy.txt')));

        if (!$proxies) return;

        Proxy::deleteAll();

        foreach ($proxies as $proxy) {
            list($host, $port) = explode(':', $proxy);
            $model = new Proxy();
            $model->host = $host;
            $model->port = $port;
            $model->save();
        }
    }

    public function actionSms()
    {

    }

    public function actionCheckAvito()
    {
        $response = file_get_contents("http://rest-app.net/api/info?login=git@anyget.ru&token=a7548861db147975e7b3ad65c09c6398");
        $response = Json::decode($response);
        if($response["status"] == "ok") {
            \Yii::$app->cache->set("avito", ArrayHelper::getValue($response, "data"));
        }
    }

    public function actionCheckAntiparkon()
    {
        //
        $response = file_get_contents("http://data.av100.ru/api.ashx?key=9f256387-5260-46b6-b9dc-8abdbfb877ee&phone=79299991975");
        $response = Json::decode($response);
        if(ArrayHelper::getValue($response, "error")) {
            \Yii::$app->cache->set("antiparkon", ArrayHelper::getValue($response, "error_msg"));
        } else {
            \Yii::$app->cache->delete("antiparkon");
        }
    }
}
