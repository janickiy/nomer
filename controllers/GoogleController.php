<?php
namespace app\controllers;

use app\models\SearchRequest;
use app\models\User;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

class GoogleController extends Controller {

    public function actionIndex() {
        $id = \Yii::$app->request->get("id");
        $uuid = \Yii::$app->request->get("uuid");
        $user = User::find()->where(["uuid" => $uuid])->one();

        if(!$user) {
            throw new BadRequestHttpException("Пользователь с uuid: ".$uuid."  не найден");
        }

        $searchRequest = SearchRequest::getDb()->cache(function () use ($id, $user) {
            return SearchRequest::find()->where(["id" => $id, "user_id" => $user->id])->one();
        });

        if(!$searchRequest) {
            throw new BadRequestHttpException("Запрос с ID: ".$id."  не найден");
        }

        return $this->render("index", [
            "searchRequest" => $searchRequest
        ]);
    }
}