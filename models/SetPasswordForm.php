<?php
namespace  app\models;

use yii\base\Model;

class SetPasswordForm extends Model {

    public $oldpassword;
    public $password;
    public $repassword;

    public function rules() {
        return [
            [['oldpassword', 'password', 'repassword'], 'required'],
            ['password', 'string', 'min' => 5],
        ];
    }

    public function attributeLabels()
    {
        return [
            "oldpassword" => "Текущий пароль",
            "password" => "Пароль",
            "repassword" => "Пароль ещё раз"
        ];
    }
}