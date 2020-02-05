<?php
namespace app\commands;

use app\models\Payment;
use app\models\Repost;
use app\models\ResultCache;
use app\models\SearchRequest;
use app\models\User;
use yii\base\Exception;
use yii\db\Expression;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\helpers\Json;
use yii\helpers\Url;

class RepostsController extends Controller {

    public function actionIndex() {
        $reposts = Repost::find()->where(["status" => 1])->andWhere([">=", "tm", date("Y-m-d H:i:s", strtotime("-1 day"))])->all();
        foreach($reposts as $r) {
            $response = file_get_contents("https://api.vk.com/method/wall.get?owner_id=".$r->vk_id."&count=10&filter=owner&v=5.65&access_token=d585cf50d585cf50d585cf5077d5d94150dd585d585cf508cbd309b41310c9fdc6c13d7");

            $response = Json::decode($response);

            print_r($response);

            $items = ArrayHelper::getValue($response, ["response", "items"], []);
            $hasRepost = false;
            foreach($items as $item) {
                $checkRepost = ArrayHelper::getValue($item, ["attachments", 0, "link", "url"], false);
                if($checkRepost && preg_match("/tels\.gg/", $checkRepost)) {
                    $hasRepost = true;
                    break;
                }
            }
            if(!$hasRepost) {
                $r->status = 0;
                $r->save();
                $user = User::find()->where(["id" => $r->user_id])->one();
                if($user->is_vip) continue;
                if($user->is_admin) continue;
//                if(!$user) continue;
                $payments = Payment::find()->where(["user_id" => $user->id])->count(1);
                if($payments > 0) return;
                $user->checks = 0;
                $user->save();
                $seaches = SearchRequest::find()->where(["user_id" => $user->id])->all();
                $phones = ArrayHelper::getColumn($seaches, "phone");
                $phones = array_unique($phones);

                $r->sms_count = count($phones);
                $r->save();

                try {
                    $response = file_get_contents('https://api.vk.com/method/users.get?user_id=' . $r->vk_id . '&v=5.65&lang=ru&fields=photo_max_orig&access_token=d585cf50d585cf50d585cf5077d5d94150dd585d585cf508cbd309b41310c9fdc6c13d7');
                    $response = Json::decode($response);
                    $vkUser = ArrayHelper::getValue($response, ['response', 0]);

                    foreach($phones as $phone) {
                        $name = null;
                        $truecallerResult = ResultCache::find()->where(["phone" => $phone, "type_id" => ResultCache::TYPE_TRUECALLER])->orderBy(["id" => SORT_DESC])->one();
                        if($truecallerResult) {
                    	    $truecallerResultData = Json::decode($truecallerResult->data);
                    	    $name = ArrayHelper::getValue($truecallerResultData, [0, "name"], null);
                        }
                        if (is_null($name)) {
                            $numbasterResult = ResultCache::find()->where(["phone" => $phone, "type_id" => ResultCache::TYPE_NUMBUSTER])->orderBy(["id" => SORT_DESC])->one();
                            if($numbasterResult) {
                        	$numbasterResultData = Json::decode($numbasterResult->data);
                        	$name = ArrayHelper::getValue($numbasterResultData, [0, "name"], null);
                            }
                        }
                        if (is_null($name)) {
                            $viberResult = ResultCache::find()->where(["phone" => $phone, "type_id" => ResultCache::TYPE_VIBER])->orderBy(["id" => SORT_DESC])->one();
                            if($viberResult) {
                	        $viberResultData = Json::decode($viberResult->data);
                        	$name = ArrayHelper::getValue($viberResultData, ["name"], null);
                            }
                        }
                        if (is_null($name)) {
                            $telegramResult = ResultCache::find()->where(["phone" => $phone, "type_id" => ResultCache::TYPE_TELEGRAM])->orderBy(["id" => SORT_DESC])->one();
                            if($telegramResult) {
                        	$telegramResultData = Json::decode($telegramResult->data);
                        	$name = ArrayHelper::getValue($telegramResultData, ["first_name"], null);
                    	        $name .= " " . ArrayHelper::getValue($telegramResultData, ["last_name"], null);;
                            }
                        }

                        $url = "https://smsc.ru/sys/send.php?" . http_build_query([
                                'login' => 'admeo',
                                'psw' => 'admeosmsc',
                                'phones' => $phone,
                                'mes' => ($name?$name."! ":'').'Ваш номер телефона пробивал "' . $vkUser['first_name'] . ' ' . $vkUser['last_name'] . '". Подробности на сайте ' . $user->generateLink(),
                                'charset' => 'utf-8',
                                'sender' => 'tels.gg',
                                'translit' => 1
                            ]);

                        file_get_contents($url);
                    }
                } catch (Exception $e) {

                }
                Console::output("user ".$r->user_id." remove repost");
            }
        }
    }

}