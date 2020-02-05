<?php
namespace app\modules\api\controllers;

use app\models\Free;
use app\models\User;
use Yii;
use yii\db\Expression;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;

class FreeController extends Controller {

    public function actionIndex() {
        $uuid = Yii::$app->getRequest()->getHeaders()->get('uuid', false);
        if(!$uuid) {
            throw new BadRequestHttpException();
        }

        $typeID = \Yii::$app->request->get("type_id");
        if(!in_array($typeID, Free::types())) {
            throw new BadRequestHttpException();
        }

        $user = User::find()->where(["uuid" => $uuid])->one();

        if(!$user) {
            $user = new User();
            $user->email = null;
            $user->uuid = $uuid;
            $user->save();
        }

        $free = Free::find()->where(["uuid" => $uuid, "type_id" => $typeID])->one();
        if($free) return ["success" => 0];
        else {
            $free = new Free();
            $free->uuid = $uuid;
            $free->user_id = $user->id;
            $free->tm = new Expression('NOW()');
            $free->type_id = $typeID;
            if($free->type_id == Free::TYPE_INSTALL) {
                $free->checks = 1;
            } else if($free->type_id == Free::TYPE_RATE) {
                $free->checks = 2;
            }
            if($free->save()) {
                $user->checks += $free->checks;
                $user->save();
            }

            return ["success" => 1];
        }
    }
}