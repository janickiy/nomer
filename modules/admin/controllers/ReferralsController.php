<?php
namespace app\modules\admin\controllers;

use app\models\User;
use yii\helpers\ArrayHelper;

class ReferralsController extends AdminController {

    public function actionIndex() {
        $users = User::find()->where([">", "ref_id", 0])->asArray()->all();

        $refIds = ArrayHelper::getColumn($users, "ref_id");
        $refIds = array_unique($refIds);
        $refs = User::find()->where(["id" => $refIds])->all();

        return $this->render("index", [
            "refs" => $refs
        ]);
    }
}