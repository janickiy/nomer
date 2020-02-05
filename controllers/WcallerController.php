<?php
namespace app\controllers;

use yii\web\Controller;

class WcallerController extends Controller {

    public function actionIndex() {
        return $this->render("index");
    }

}