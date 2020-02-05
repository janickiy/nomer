<?php
namespace app\controllers;

use app\models\UrlFilter;
use yii\web\Controller;

class UrlController extends Controller {

    public $enableCsrfValidation = false;

    public function actionIndex() {
        $url = \Yii::$app->request->post("url");
        $type = \Yii::$app->request->post("type");

        $u = UrlFilter::find()->where(["url" => $url])->one();
        if(is_null($u)) {
            $u = new UrlFilter();
            $u->url = $url;
        }
        $u->type = $type;
        $u->save();

        return 1;
    }

}