<?php
namespace app\modules\api\controllers;

use app\models\RequestResult;
use app\models\ResultCache;
use app\models\SearchRequest;
use app\models\User;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;

class HistoryController extends Controller {

    public function actionIndex() {
        $userId = Yii::$app->request->get("id", false);
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

        $data = [];
        foreach(SearchRequest::find()->where(["user_id" => $user->id])->orderBy(["id" => SORT_DESC])->limit(20)->all() as $sr) {
            $operatorRow = RequestResult::find()->where(["request_id" => $sr->id, "type_id" => ResultCache::TYPE_OPERATOR])->one();
            $operator = [];
            if($operatorRow) {
                $operator = Json::decode($operatorRow->data);
            }

            $names = [];

            $namesRows = RequestResult::find()->where(["request_id" => $sr->id, "type_id" => [
                ResultCache::TYPE_TRUECALLER,
                ResultCache::TYPE_NUMBUSTER
            ]])->all();
            foreach ($namesRows as $namesRow) {
                $nameData = Json::decode($namesRow->data);
                $names = ArrayHelper::merge($names, ArrayHelper::getColumn($nameData, "name"));
            }

            $names = array_unique($names);

            if(count($names) < 2) {
                $namesRows = RequestResult::find()->where(["request_id" => $sr->id, "type_id" => [
                    ResultCache::TYPE_FACEBOOK,
                    ResultCache::TYPE_VK_2012,
                    ResultCache::TYPE_VK_OPEN,
                    ResultCache::TYPE_VIBER,
                    ResultCache::TYPE_TELEGRAM,
                    ResultCache::TYPE_VK,
                    ResultCache::TYPE_AVITO,
                ]])->all();
                foreach ($namesRows as $namesRow) {
                    $nameData = Json::decode($namesRow->data);
                    $names = ArrayHelper::merge($names, ArrayHelper::getColumn($nameData, "name"));
                }
            }

            $names = array_unique($names);
            $names = array_splice($names, 0, 2);

            $data[] = [
                "id"    => $sr->id,
                "phone" => $sr->phone,
                "names" => $names,
                "is_payed" => $sr->is_payed?1:0,
                "index" => array_sum(ArrayHelper::getColumn($sr->results, "index")),
                "operator" => ArrayHelper::getValue($operator,"operator", "не известно"),
                "region" => ArrayHelper::getValue($operator,"region", "не известно"),
            ];
        }

        return $data;
    }

}