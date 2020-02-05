<?php

namespace app\models;

use app\components\FindPhoneValidator;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "phone_request".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $tm
 * @property string $ip
 * @property string $data
 * @property string $wallet
 * @property string $contact
 * @property integer $status
 */
class PhoneRequest extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'phone_request';
    }

    public function behaviors()
    {
        return [
            'user_id' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['user_id'],
                ],
                'value' => Yii::$app->request->isConsoleRequest?"":\Yii::$app->getUser()->getId(),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['data'], 'required'],
            ['data', FindPhoneValidator::className()],
            [['user_id', 'status'], 'integer'],
            [['tm'], 'safe'],
            [['data'], 'string'],
            [['ip', 'wallet', 'contact'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'tm' => 'Tm',
            'ip' => 'Ip',
            'data' => 'Введите адрес страницы Вконтакте, Facebook, Instagram или email',
            'wallet' => 'Кошелек',
            'contact' => 'Контактная информация',
            'status' => 'Status',
        ];
    }
}
