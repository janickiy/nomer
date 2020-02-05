<?php
namespace app\models\forms;

use yii\base\Model;

class BlockForm extends Model {

    public $reCaptcha;
    public $phone;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['phone'], 'required'],
            [['phone'], 'string', 'min' => 10],
            [['reCaptcha'], \himiklab\yii2\recaptcha\ReCaptchaValidator::className(), 'secret' => '6LdpNCMUAAAAABTYWw_Eaca7iGlbXaCWWe0fqqp7', 'uncheckedMessage' => 'Пожалуйста, подтвержите, что вы не бот!']
        ];
    }

}