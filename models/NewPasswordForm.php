<?php
namespace  app\models;

use yii\base\Model;

class NewPasswordForm extends Model {

    public $password;
    public $repassword;

    public function rules() {
        return [
            [['password', 'repassword'], 'required'],
            ['password', 'string', 'min' => 5],
        ];
    }

    public function attributeLabels()
    {
        return [
            "password" => "Пароль",
            "repassword" => "Пароль ещё раз"
        ];
    }
}