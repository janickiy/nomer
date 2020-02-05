<?php
namespace app\modules\api\controllers;

use app\models\Auth;
use app\models\User;
use Yii;
use yii\db\Expression;
use yii\rest\Controller;

class SigninController extends Controller {

    public function actionExit() {
        /*
        $uuid = Yii::$app->getRequest()->getHeaders()->get('uuid', false);
        $user = User::find()->where(["uuid" => $uuid])->andWhere(["IS NOT", "email", null])->one();
        if($user) {
            $user->uuid = null;
            $user->save();
        }
        $userFree = User::find()->select(["id", "checks"])->where(["uuid" => $uuid])->andWhere(["email" => null])->one();
        if(!$userFree) {
            $userFree = new User();
            $userFree->email = null;
            $userFree->uuid = $uuid;
            $userFree->save();
        }
        return $userFree;
        */
        return ["success" => 1];
    }

    public function actionReg() {
        $uuid = Yii::$app->getRequest()->getHeaders()->get('uuid', false);

        $email = \Yii::$app->request->post("email");
        $password = \Yii::$app->request->post("password");

        $user = User::find()->where(["email" => $email])->one();
        if($user) {
            return ["error" => 1];
        } else {
            $user = new User();
            $user->email = $email;
            $user->password = $password;
            $user->uuid = $uuid;
            $user->save();
            return $user;
        }
    }

    public function actionIndex() {
        $uuid = Yii::$app->getRequest()->getHeaders()->get('uuid', false);

        $email = \Yii::$app->request->post("email");
        $password = \Yii::$app->request->post("password");
        
        $user = User::find()->where(["email" => $email])->one();
        if($user && $user->validatePassword($password)) {
            $user->uuid = $uuid;
            $user->save();
            return [
                "id" => $user->id,
                "checks" => $user->checks,
		        "email" => $user->email
            ];
        }
        return [
            "id" => 0,
            "checks" => 0
        ];
    }

    public function actionGoogle() {
        $uuid = Yii::$app->getRequest()->getHeaders()->get('uuid', false);

        $id = \Yii::$app->request->post("id");
        $email = \Yii::$app->request->post("email");
        $user = User::find()->where(["email" => $email])->one();
        if(!$user) {
            $password = Yii::$app->security->generateRandomString(6);
            $user = new User([
                'email' => mb_strtolower($email),
                'password' => $password,
                'is_confirm' => true,
                'tm_confirm' => new Expression('NOW()')
            ]);

            $user->uuid = $uuid;

            if ($user->save()) {
                $auth = new Auth([
                    'user_id' => $user->id,
                    'source' => "google",
                    'source_id' => (string)$id,
                ]);
                $auth->save();
            }
        }
        return [
            "id" => $user->id,
            "checks" => $user->checks
        ];
    }

    public function actionVk() {
        $uuid = Yii::$app->getRequest()->getHeaders()->get('uuid', false);

        $id = \Yii::$app->request->post("id");
        $email = \Yii::$app->request->post("email");
        $user = User::find()->where(["email" => $email])->one();
        if(!$user) {
            $password = Yii::$app->security->generateRandomString(6);
            $user = new User([
                'email' => mb_strtolower($email),
                'password' => $password,
                'is_confirm' => true,
                'tm_confirm' => new Expression('NOW()')
            ]);

            $user->uuid = $uuid;

            if ($user->save()) {
                $auth = new Auth([
                    'user_id' => $user->id,
                    'source' => "vk",
                    'source_id' => (string)$id,
                ]);
                $auth->save();
            }
        }
        return [
            "id" => $user->id,
            "checks" => $user->checks
        ];
    }

    public function actionFacebook() {
        $uuid = Yii::$app->getRequest()->getHeaders()->get('uuid', false);

        $id = \Yii::$app->request->post("id");
        $email = \Yii::$app->request->post("email");
        $user = User::find()->where(["email" => $email])->one();
        if(!$user) {
            $password = Yii::$app->security->generateRandomString(6);
            $user = new User([
                'email' => mb_strtolower($email),
                'password' => $password,
                'is_confirm' => true,
                'tm_confirm' => new Expression('NOW()')
            ]);

            $user->uuid = $uuid;

            if ($user->save()) {
                $auth = new Auth([
                    'user_id' => $user->id,
                    'source' => "facebook",
                    'source_id' => (string)$id,
                ]);
                $auth->save();
            }
        }
        return [
            "id" => $user->id,
            "checks" => $user->checks
        ];
    }
}
