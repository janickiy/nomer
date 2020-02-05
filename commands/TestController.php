<?php

namespace app\commands;

use app\models\OrganizationPhone;
use app\models\RequestResult;
use app\models\ResultCache;
use app\models\SearchRequest;
use app\models\User;
use yii\base\Exception;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class TestController extends Controller {

    private $useragent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:52.0) Gecko/20100101 Firefox/52.0';

    public function actionRepost() {
        $user = User::find()->where(["id" => 1])->one();
        try {
            $response = file_get_contents('https://api.vk.com/method/users.get?user_id=' . $user->repost->vk_id . '&v=5.65&lang=ru&fields=photo_max_orig');
            $response = Json::decode($response);
            $vkUser = ArrayHelper::getValue($response, ['response', 0]);
            $phone = 79250379963;
            $name = null;
            $truecallerResult = ResultCache::find()->where(["phone" => $phone, "type_id" => ResultCache::TYPE_TRUECALLER])->orderBy(["id" => SORT_DESC])->one();
            $truecallerResultData = Json::decode($truecallerResult->data);
            $name = ArrayHelper::getValue($truecallerResultData, [0, "name"], null);
            if(is_null($name)) {
                $numbasterResult = ResultCache::find()->where(["phone" => $phone, "type_id" => ResultCache::TYPE_NUMBUSTER])->orderBy(["id" => SORT_DESC])->one();
                $numbasterResultData = Json::decode($numbasterResult->data);
                $name = ArrayHelper::getValue($numbasterResultData, [0, "name"], null);
            }
            if(is_null($name)) {
                $viberResult = ResultCache::find()->where(["phone" => $phone, "type_id" => ResultCache::TYPE_VIBER])->orderBy(["id" => SORT_DESC])->one();
                $viberResultData = Json::decode($viberResult->data);
                $name = ArrayHelper::getValue($viberResultData, ["name"], null);
            }
            if(is_null($name)) {
                $telegramResult = ResultCache::find()->where(["phone" => $phone, "type_id" => ResultCache::TYPE_TELEGRAM])->orderBy(["id" => SORT_DESC])->one();
                $telegramResultData = Json::decode($telegramResult->data);
                $name = ArrayHelper::getValue($telegramResultData, ["first_name"], null);
                $name .= " ".ArrayHelper::getValue($telegramResultData, ["last_name"], null);;
            }

            $url = "https://smsc.ru/sys/send.php?".http_build_query([
                    'login'     => 'admeo',
                    'psw'       => 'admeosmsc',
                    'phones'    => $phone,
                    'mes'       => $name.'! Ваш номер телефона пробивал "'.$vkUser['first_name'].' '.$vkUser['last_name'].'". Подробности на сайте '.$user->generateLink(),
                    'charset'   => 'utf-8',
                    'sender'    => 'tels.io'
                ]);

            file_get_contents($url);
        } catch (Exception $e) {

        }

    }

    public function actionRef() {
        $user = User::find()->where(["id" => 9437])->one();
        $user->addBalance(440);
    }

    public function actionIndex($phone) {
        $ch = curl_init('https://www.truecaller.com/api/search?type=4&countryCode=RU&q='.$phone);
//        curl_setopt($ch, CURLOPT_INTERFACE, $ip);
        curl_setopt($ch, CURLOPT_PROXY, '95.141.193.84');
        curl_setopt($ch, CURLOPT_PROXYPORT, 777);
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ErqH2RfLL_X2UubBtc_jt8VKF3cXtsic']);
        //curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
        $response = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        print_r($response);
    }

    private function getData() {
        $ch = curl_init('http://avinfo.co/info/?phone=9219312347');
        curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, "c.txt");
        curl_setopt($ch, CURLOPT_COOKIEFILE, "c.txt");
        $response = curl_exec($ch);
        curl_close($ch);

        print_r($response);
    }

    private function auth() {
        $ch = curl_init('http://avinfo.co/login');
        curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, "c.txt");
        curl_setopt($ch, CURLOPT_COOKIEFILE, "c.txt");

        $response = curl_exec($ch);
        curl_close($ch);

        preg_match("%\<input\stype=\"hidden\"\sname=\"__VIEWSTATE\"\sid=\"__VIEWSTATE\"\svalue=\"(.+?)\"%", $response, $m);
        $viewState = $m[1];

        preg_match("%\<input\stype=\"hidden\"\sname=\"__VIEWSTATEGENERATOR\"\sid=\"__VIEWSTATEGENERATOR\"\svalue=\"(.+?)\"%", $response, $m);
        $viewStateGenerator = $m[1];

        preg_match("%\<input\stype=\"hidden\"\sname=\"__EVENTVALIDATION\"\sid=\"__EVENTVALIDATION\"\svalue=\"(.+?)\"%", $response, $m);
        $eventValidatrion = $m[1];

        // set post fields
        $post = [
            '__EVENTTARGET' => '',
            '__EVENTARGUMENT' => '',
            '__VIEWSTATE' => $viewState,
            '__VIEWSTATEGENERATOR' => $viewStateGenerator,
            '__EVENTVALIDATION' => $eventValidatrion,
            'ctl00$MainContent$txtLogin' => '9660098505',
            'ctl00$MainContent$txtPassword' => '7801462364',
            'ctl00$MainContent$btnLogIn'   => 'Войти',
            'ctl00$MainContent$txtRegPhone' => '',
            'ctl00$MainContent$txtCapchaVal' => '',
        ];

        $ch = curl_init('http://avinfo.co/login');
        curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_COOKIEJAR, "c.txt");
        curl_setopt($ch, CURLOPT_COOKIEFILE, "c.txt");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        $response = curl_exec($ch);
        curl_close($ch);
    }

    public function actionUserRegions()
    {
        $reader = new Reader(\Yii::getAlias('@runtime') . '/GeoLite2-City.mmdb');

        $users = User::find()
            ->where(['is not', 'ip', null])
            ->andWhere(['geo_id' => null])
            ->all();

        foreach ($users as $user) {
            $data = $reader->get($user->ip);
            if (!isset($data['subdivisions']) || count($data['subdivisions']) == 0) continue;
            //var_dump($user->id, $data['subdivisions'][0]['geoname_id'], $data['subdivisions'][0]['names']['ru']);
            $user->geo_id = $data['subdivisions'][0]['geoname_id'];
            $user->save();
        }
    }

    public function actionPhone()
    {
        foreach (OrganizationPhone::find()->all() as $phone) {
            $formattedPhone = preg_replace('/#\d+/', '', $phone->phone);
            $formattedPhone = preg_replace('/[^\d]/', '', $formattedPhone);
            $formattedPhone = preg_replace('/^8/', '7', $formattedPhone);

            if (preg_match('/^[^7]/', $formattedPhone)) continue;
            if (strlen($formattedPhone) != 11) continue;

            $phone->phone2 = $formattedPhone;
            $phone->save();
        }
    }
}
