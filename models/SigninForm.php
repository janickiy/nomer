<?php
namespace  app\models;

use app\components\LoginValidator;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use \yii\db\Expression;

class SigninForm extends Model {

    public $email;
    public $password;

    public function rules() {
        return [
            ['password', 'string', 'min' => 4],
            ['email', 'filter', 'filter' => 'trim'],
            ['email', LoginValidator::className()],
            [['password', 'email'], 'required']
        ];
    }

    public function attributeLabels() {
        return [
            "email" => "E-mail или номер телефона",
            'password' => 'Пароль'
        ];
    }

    public function login() {
        $user = User::findByEmail($this->email);

        if(!is_null($user) && $user->validatePassword($this->password)) {
            $resultLogin = \Yii::$app->getUser()->login($user, 3600 * 24 * 90);
            if($resultLogin) {
                $site = Site::find()->where(["name" => $_SERVER["HTTP_HOST"]])->one();
                $log = new UserAuthLog();
                $log->user_id = $user->id;
                $log->site_id = ArrayHelper::getValue($site, "id", 0);
                $log->ip = \Yii::$app->request->getUserIP();
                $log->tm = new Expression('NOW()');
                $log->save();
            }
            return $resultLogin;
        }

        return false;
    }
}