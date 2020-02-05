<?php
namespace app\modules\admin\controllers;

use app\components\coupon;
use app\models\PlatiCode;
use yii\data\ActiveDataProvider;

class PlatiController extends AdminController {

    public function actionNew() {
        $checks = \Yii::$app->request->get("checks");
        $count = \Yii::$app->request->get("count");

        $codes = coupon::generate_coupons($count, [
            "length" => 12,
            "letters" => true,
            "numbers" => true,
            "symbols" => false,
            "mixed_case" => true,
            "mask" => "XXX-XXX-XXX-XXX"
        ]);

        $newCodes = [];
        foreach($codes as $code) {
            $platiCode = PlatiCode::find()->where(["code" => $code])->one();
            if($platiCode) continue;
            $platiCode = new PlatiCode();
            $platiCode->code = $code;
            $platiCode->checks = $checks;
            $platiCode->save();
            $newCodes[] = $code."<br>Для зачисления проверок введите код на сайте https://nomer.io/pay/coupon";
        }
        $file = \Yii::getAlias('@runtime')."/".uniqid("coupons").".txt";
        file_put_contents($file, join("\n", $newCodes));

        \Yii::$app->response->sendFile($file);
    }

    public function actionIndex() {
        $dataProvider = new ActiveDataProvider([
            'query' => PlatiCode::find()
        ]);

        return $this->render('index', ["dataProvider" => $dataProvider]);
    }
}