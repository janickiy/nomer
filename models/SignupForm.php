<?php
namespace  app\models;

use app\components\LoginValidator;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class SignupForm extends Model {

    public $email;
    public $password;
    public $repassword;
    public $agree;

    public function rules() {
        return [
            [['password', 'email'], 'required'],
            ['password', 'string', 'min' => 4],
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'filter', 'filter' => 'strtolower'],
            ['email', LoginValidator::className()],
            ['email', 'unique', 'targetClass' => '\app\models\User', 'targetAttribute' => 'email', 'message' => 'Этот логин уже используется'],
        ];
    }

    public function createUser() {
        $user = new User([
            'email'     => $this->email,
            'password'  => $this->password,
        ]);
        $user->auth_key = \Yii::$app->getSecurity()->generateRandomString();
        $ref = \Yii::$app->session->get("ref_id");
        $ref = explode("-", $ref);
        $refID = ArrayHelper::getValue($ref, 0, 0);
        $refTM = ArrayHelper::getValue($ref, 1, 0);
        if($refID && time() - $refTM <= 60 * 60 * 24 * 7) {
            $user->ref_id = \Yii::$app->session->get("ref_id");
        }

        $user->save();

        return $user;
    }

    public function attributeLabels()
    {
        return [
            "email" => "E-mail или номер телефона",
            "password" => "Пароль"
        ];
    }
}