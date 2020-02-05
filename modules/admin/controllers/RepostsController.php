<?php
namespace app\modules\admin\controllers;

use app\models\Repost;
use yii\data\ActiveDataProvider;

class RepostsController extends AdminController {

    public function actionIndex() {
        $dataProvider = new ActiveDataProvider([
            "query" => Repost::find()->orderBy(["tm" => SORT_DESC])
        ]);

        return $this->render("index", [
            "dataProvider" => $dataProvider
        ]);
    }
}
