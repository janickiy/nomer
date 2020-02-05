<?php
namespace app\controllers;

use app\models\SetPasswordForm;
use yii\web\Controller;

class SettingsController extends Controller {

    public function actionIndex() {
        /* @var $user \app\models\User */
        $user = \Yii::$app->getUser()->getIdentity();
        $model = new SetPasswordForm();
        if($model->load(\Yii::$app->request->post()) && $user->validatePassword($model->oldpassword) && $model->validate()) {
            $user->password = $model->password;
            if($user->save()) {
                \Yii::$app->session->setFlash("success", "Пароль успешно изменен!");
                return $this->refresh();
            }
        }

        return $this->render('index', [
            "model" => $model
        ]);
    }
}