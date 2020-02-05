<?php
namespace app\commands;

use app\models\RequestResult;
use app\models\SearchRequest;
use app\models\User;
use Swift_Attachment;
use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\swiftmailer\Message;

class RetargetController extends Controller {

    const USERID = 113168;

    public function actionIndex() {
        $lastUserId = \Yii::$app->cache->get("lastUserId");
        if(!$lastUserId) $lastUserId = 0;
        foreach (User::find()->where(["IS NOT", "email", null])->andWhere([">", "id", $lastUserId])->with(["payments"])->orderBy(["id" => SORT_ASC])->batch(100) as $users) {
            foreach($users as $user) {
                \Yii::$app->cache->set("lastUserId", $user->id);
                if(!preg_match("/@/", $user->email)) continue;
                if(count($user->payments)) continue;

                echo "\n\nUSERID: ".$user->id."\n";

                $ch = curl_init('http://ssd.nomer.io/api/' . $user->email . '?token=NWBpdeqbbAFJMVYJU6XAfhyydeyhgX');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($httpCode == 200) { // Все ок, берем данные
                    $response = Json::decode($response);
                    $vkId = 0;
                    foreach($response as $r) {
                        if(ArrayHelper::getValue($r, "type") == "profile_id") {
                            $vkId = ArrayHelper::getValue($r, "data");
                            break;
                        }
                    }
                    if($vkId) {
                        $socData = file_get_contents("https://api.vk.com/method/users.get?user_ids=" . $vkId . "&fields=sex,bdate&lang=ru&access_token=8f95fab19fb8d3d41bdeeb28f0112cb2cd3c86a93fc66acbd29f327d1aa3f196540bfe10dcd4ca97baf37");
                        $socData = Json::decode($socData);
                        $socData = $socData["response"][0];

                        $bdate = ArrayHelper::getValue($socData, "bdate", null);
                        $sex = ArrayHelper::getValue($socData, "sex", null);
                        if(!$bdate) continue;
                        if($sex != 2) continue;

                        $bdate = explode(".", $bdate);
                        $year = ArrayHelper::getValue($bdate, 2);

                        if(date("Y") - $year >= 25) {
                            $phones = [];
                            $searches = SearchRequest::find()->where(["user_id" => $user->id])->asArray()->all();
                            foreach($searches as $s) {
                                if(!isset($phones[$s["phone"]])) {
                                    $phones[$s["phone"]] = 1;
                                } else {
                                    $phones[$s["phone"]]++;
                                }
                            }
                            arsort($phones);
                            foreach($phones as $p => $c) {
                                echo "Phone: ".$p."\n";
                                $ch = curl_init('http://viber.apinomer.com:8999/' . preg_replace("/^7/", "8", $p));
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                $response = curl_exec($ch);
                                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                $searchRequest = SearchRequest::find()->where(["user_id" => self::USERID, "phone" => $p])->orderBy(["id" => SORT_DESC])->asArray()->one();
                                $requestResults = RequestResult::find()->select(["id", "index", "request_id"])->where(["request_id" => ArrayHelper::getValue($searchRequest, "id")])->all();
                                $index = array_sum(ArrayHelper::getColumn($requestResults, "index"));
                                echo "Index: ".$index."\n";
                                if($index >= 50) {
                                    $mail = Yii::$app->mailer
                                        ->compose("retarget", [
                                            "phone"         => preg_replace("/^7/", "8", $p),
                                            "logo"          => 'https://nomer.io/img/m/logo.png',
                                            "screenshot"    => 'http://viber.apinomer.com:8999/static/' . preg_replace("/^7/", "8", $p).'.png'
                                        ])
                                        ->setFrom('no-reply@nomer.io')
                                        ->setTo($user->email)
                                        ->setSubject("Вы искали номер ". preg_replace("/^7/", "8", $p))
                                        ->send();

                                    //$mail->compose("retarget", ["phone" => preg_replace("/^7/", "8", $p)])
                                    var_dump($mail);
                                    break;
                                }
                            }
                        }

                    }
                }
            }
        }
    }
}