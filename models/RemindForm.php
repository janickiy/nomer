<?php
namespace  app\models;

use yii\base\Model;

class RemindForm extends Model {

    public $email;

    public function rules() {
        return [
            ['email', 'email'],
        ];
    }

    public function remind() {
        if(trim($this->email) == "") return false;
        $user = User::findByEmail($this->email);
        if(!$user) return false;
        $user->generatePasswordResetToken();

        return $user->save();
    }
}