<?php
namespace app\modules\api\controllers;

use app\components\SearchHelper;
use app\models\BlockPhone;
use app\models\SearchRequest;
use app\models\User;
use Yii;
use yii\base\Exception;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;

class SearchController extends Controller{

    public function actionIndex() {
        $phone = \Yii::$app->request->get('phone');
        $phone = preg_replace('/\D/', '', $phone);

        if (mb_strlen($phone) == 10) {
            $phone = '7' . $phone;
        } else {
            $phone = preg_replace('/^8/', '7', $phone);
        }

        $source = null;

        $userId = Yii::$app->request->get("id", false);
        $isAndroid = Yii::$app->getRequest()->getHeaders()->get('isandroid', false);
        $uuid = Yii::$app->getRequest()->getHeaders()->get('uuid', false);
        if(!$uuid && !$userId) {
            throw new BadRequestHttpException();
        }

        /* @var $user User */
        if($userId) {
            $user = User::find()->where(["id" => $userId])->one();
            if($uuid) {
                $user->uuid = $uuid;
                $user->save();
            }
        } elseif($uuid) {
            $user = User::find()->where(compact('uuid'))->one();

            if (!$user) {
                $user = new User();
                $user->email = null;
                $user->uuid = $uuid;
                $user->save();
            }
        }

        $count = SearchRequest::find()->where(["user_id" => $user->id])->count(1);

        if(!$user->is_vip) {
            $block = BlockPhone::find()->where(["phone" => $phone, "status" => [1]])->one();
            if(!is_null($block)) {
                return ['id' => -1];
            }
        }

        $limit = 1;
        $countSeaches = 0;
        if($user->checks == 0) {
            $results = 0;

            $freePhones = [];
            $users = User::find()->where(["uuid" => $user->uuid])->all();
            foreach($users as $u) {
                $searchRequests = SearchRequest::find()->where(["user_id" => $u->id])->andWhere([">", "tm", date("Y-m-d H:i:s", strtotime("-7 days"))])->all();
                foreach ($searchRequests as $s) {
                    if($s->is_has_name && $s->is_has_photo && !in_array($s->phone, $freePhones)) {
                        $countSeaches++;
                        $freePhones[] = $s->phone;
                    }
                    if($countSeaches == $limit) break;
                }
            }

            if($countSeaches >= $limit) return ['id' => 0];
        }

        $isCache = 0;
        //$searchRequest = SearchRequest::find()->where(["user_id" => $user->id, "phone" => $phone])->orderBy(["id" => SORT_DESC])->one();
        $searchRequest = false;
        $refresh = \Yii::$app->request->get("refresh", 0);

        if($phone == "79645552229") {
            $refresh = 1;
        }
        if(!$refresh) {
            $searchRequest = SearchRequest::find()->with('results')->where([
                "user_id"   => $user->id,
                "phone"     => $phone,
                "is_payed" => [1, 2]
            ])->orderBy(["id" => SORT_DESC])->one();
            if($searchRequest) {
                $isCache = 1;
            }
        }
        if(!$searchRequest) {
            $isCache = 0;
            $searchRequest = new SearchRequest();
            $searchRequest->ip = \Yii::$app->request->userIP;
            $searchRequest->ua = \Yii::$app->request->userAgent;
            $searchRequest->phone = $phone;
            $searchRequest->tm = new Expression('NOW()');
            $searchRequest->user_id = $user->id;
            $searchRequest->refresh = true;
            if($isAndroid) {
                $searchRequest->source_id = SearchRequest::SOURCE_ANDROID;
            } else {
                $searchRequest->source_id = SearchRequest::SOURCE_IOS;
            }

            if($user->checks < 0) {
                $searchRequest->is_payed = 1;
            } elseif($user->checks > 0) {
                $user->checks--;
                $searchRequest->is_payed = 1;
            } elseif($user->balance >= \Yii::$app->params["cost"]) {
                $user->balance -= \Yii::$app->params["cost"];
                $searchRequest->is_payed = 1;
            } elseif(!$isAndroid) {
                $searchRequest->is_payed = 1;
            } elseif($countSeaches == 0) {
                $searchRequest->is_payed = 1;
            }
            $user->save();

            $searchRequest->save();
        }

//        if($uuid == "AA4AD41A-E2CE-4A7B-8AA0-FCE2EFAE52EE") $count = 0;

        $result = [
            'id' => $searchRequest->id,
            'mobile' => null,
            'is_payed' => (int)$searchRequest->is_payed,
            'is_cache' => $isCache,
            'is_first' => $count?0:1
        ];

        $operator = SearchHelper::Operator($phone);

        if($operator) {
            $result['mobile'] = $operator;
        }

        return $result;
    }
}
