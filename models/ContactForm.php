<?php
namespace app\models;

class ContactForm extends \yii\base\Model {

    public $email;
    public $message;

    public $reCaptcha;

    public function rules() {
        return [
            ["email", "email"],
            ["message", "safe"],
            [["email", "message"], "required"],
            [['reCaptcha'], \himiklab\yii2\recaptcha\ReCaptchaValidator::className(), 'secret' => '6LdpNCMUAAAAABTYWw_Eaca7iGlbXaCWWe0fqqp7', 'uncheckedMessage' => 'Пожалуйста, подтвержите, что вы не бот!']
        ];
    }

    public function attributeLabels()
    {
        return [
            "email" => "Ваш E-mail адреса",
            "message" => "Сообщение"
        ];
    }
}