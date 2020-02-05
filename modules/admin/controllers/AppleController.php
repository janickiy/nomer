<?php
namespace app\modules\admin\controllers;

use app\models\AppleSubscribeEvent;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class AppleController extends AdminController {

    public function actionIndex() {
        $query = AppleSubscribeEvent::find();
        $subId = \Yii::$app->request->get("sub_id");
        if($subId) {
            $query->andWhere(["subscription_id" => $subId]);
        }
        $tm_start = \Yii::$app->request->get("tm_start");
        if($tm_start) {
            $query->andWhere([">=", "original_start_date", $tm_start]);
        }

        $tm_end = \Yii::$app->request->get("tm_end");
        if($tm_end) {
            $query->andWhere(["<=", "original_start_date", $tm_end]);
        }


        $dataProvider = new ActiveDataProvider([
            "query" => $query
        ]);

        $sData = AppleSubscribeEvent::find()->select(["subscription_id", "subscription_name"])->groupBy(["subscription_id", "subscription_name"])->all();
        $subs = ArrayHelper::map($sData, "subscription_id", "subscription_name");

        return $this->render("index", [
            "dataProvider" => $dataProvider,
            "subs" => $subs,
            "subId" => $subId,
            "tm_start" => $tm_start,
            "tm_end" => $tm_end
        ]);
    }
}