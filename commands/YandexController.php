<?php
namespace app\commands;

use yii\console\Controller;
use yii\helpers\Json;

class YandexController extends Controller{

    public function actionIndex() {
        $login = 'mezhevikina.masha@yandex.ru';
        $password = 'Ag6K2oxG';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://passport.yandex.ru/auth');
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'login='.urlencode($login).'&passwd='.urlencode($password)."&retpath=".urlencode("https://money.yandex.ru/new")."&from=money&origin&timestamp");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_COOKIEJAR, \Yii::getAlias('@runtime')."/ya.cookie");
        curl_setopt($ch, CURLOPT_COOKIEFILE, \Yii::getAlias('@runtime')."/ya.cookie");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");
        $response = curl_exec($ch);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://money.yandex.ru/ajax/history/partly?history_shortcut=history_all&search=&start-record=0&record-count=10');
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_COOKIEJAR, \Yii::getAlias('@runtime')."/ya.cookie");
        curl_setopt($ch, CURLOPT_COOKIEFILE, \Yii::getAlias('@runtime')."/ya.cookie");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");

        $response = curl_exec($ch);

        echo $response;

        die();

        if(preg_match_all('/\<form(.+?)action=\"(.+?)\"\>/u', $response, $m) && preg_match_all('/\"sk\"\:\"(.+?)\"/u', $response, $m2)) {
            $requestURL = $m[2][1];
            $sk = $m2[1][0];

            /*
             * protection-period:7
protection-code:0962
receiver:410014057045840
comment:
sum_k:10
sum:10.05
pay-money-source:yamoney-account-410011204915798
sk:ud50ce5e2d22b9fe711537a23f95bf18b
             */
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $requestURL);
            curl_setopt($ch, CURLOPT_POSTFIELDS, 'receiver='.urlencode("410014057045840")."&sum=10.05&sum_k=10&protection-period=7&protection-code=0962&pay-money-source=yamoney-account-410011204915798&sk=".$sk);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_REFERER, "https://money.yandex.ru/transfer");
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_COOKIEJAR, \Yii::getAlias('@runtime')."/ya.cookie");
            curl_setopt($ch, CURLOPT_COOKIEFILE, \Yii::getAlias('@runtime')."/ya.cookie");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");
            $response = curl_exec($ch);

            $result = Json::decode($response);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, preg_replace("/contract/", "process", $result["url"])."/PC/sign");
            curl_setopt($ch, CURLOPT_POSTFIELDS, "pay-money-source=yamoney-account-410011204915798&sk=".$sk);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_REFERER, $result["url"]);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_COOKIEJAR, \Yii::getAlias('@runtime')."/ya.cookie");
            curl_setopt($ch, CURLOPT_COOKIEFILE, \Yii::getAlias('@runtime')."/ya.cookie");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");
            $response = curl_exec($ch);

            print_r($response);
        }




    }
}