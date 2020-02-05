<?php
namespace app\modules\api\controllers;

use app\models\RequestResult;
use app\models\ResultCache;
use app\models\SearchRequest;
use app\models\User;
use Yii;
use yii\base\Exception;
use yii\helpers\Json;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\Response;

class ResultController extends Controller {

    public function actionIndex($id) {
        //\Yii::$app->response->format = Response::FORMAT_RAW;
        $uuid = Yii::$app->getRequest()->getHeaders()->get('uuid', false);
        $user_id = \Yii::$app->request->get("user_id", false);
        if(!$uuid && !$user_id) {
            throw new BadRequestHttpException();
        }

        if($user_id) {
            $user = User::find()->where(["id" => $user_id])->one();
        } else {
            $user = User::find()->where(["uuid" => $uuid])->one();
        }
        if(!$user) {
            throw new BadRequestHttpException();
        }

        $searchRequest = SearchRequest::find()->where(["id" => $id, "user_id" => $user->id])->one();
        if(!$searchRequest) {
            throw new BadRequestHttpException();
        }

        $results = [];
        $resultsData = RequestResult::find()->where(["request_id" => $id])->all();
        foreach($resultsData as $r) {
            try {
                $data = Json::decode($r->data);
                if($r->type_id == ResultCache::TYPE_VIBER) {
                    $data = [$data];
                }
                sort($data);

                if($r->type_id == ResultCache::TYPE_AVITO) {
                    foreach ($data as $i => $row) {
                        $base64images = [];
                        if (isset($row["images"])) {
                            $images = explode(",", $row["images"]);
                            foreach ($images as $image) {
                                if(file_exists(Yii::$app->params['files'] . '/' . $image)) {
                                    $i = @file_get_contents(Yii::$app->params['files'] . '/' . $image);
                                    if($i) {
                                        $b64 = @base64_encode($i);
                                        if($b64) {
                                            $base64images[] = $b64;
                                        }
                                    }
                                }
                            }
                        }
                        /*
                        $ijson = json_encode($base64images);
                        if($ijson) {
                            $data[$i]["images"] = $base64images;
                        }
                        */
                    }
                }

                $result = [
                    "type" => ResultCache::getTypeSysname($r->type_id),
                    "elements" => $data
                ];
                if($r->type_id == ResultCache::TYPE_AVINFO_API) {
                    $result["avinfo"] = $data;
                    $result["type"] = "avinfo";
                }
                if($r->type_id == ResultCache::TYPE_GIBDD) {
                    $result["gibdd"] = $data;
                    $result["type"] = "avinfo";
                }
                if($r->type_id == ResultCache::TYPE_ANTIPARKON) {
                    $result["antiparkon"] = $data;
                    $result["type"] = "avinfo";
                }
                $results[] = $result;
            } catch (Exception $e) {

            }

        }
        
        return $results;
    }
}