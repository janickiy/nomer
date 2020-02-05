<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "payments".
 *
 * @property integer $id
 * @property string $sum
 * @property string $amount
 * @property integer $user_id
 * @property string $tm
 * @property string $operation_id
 * @property string $operation_label
 * @property integer $type_id
 * @property integer $site_id
 * @property User $user
 * @property string $source
 */
class Payment extends \yii\db\ActiveRecord
{

    const TYPE_YANDEX  = 1;
    const TYPE_QIWI = 2;
    const TYPE_WEBMONEY = 3;
    const TYPE_YANDEX_WALLET = 4;
    const TYPE_QIWI_TERMINAL = 5;
    const TYPE_COUPON = 6;
    const TYPE_ANDROID = 7;
    const TYPE_TESTAPPLE = 8;
    const TYPE_APPLE = 9;

    public static function primaryKey()
    {
        return ["id"];
    }

    public static function getTypeName($type_id) {
        switch ($type_id) {
            case Payment::TYPE_QIWI: return "Qiwi Wallet";
            case Payment::TYPE_YANDEX: return "Яндекс.Деньги Card";
            case Payment::TYPE_WEBMONEY: return "WebMoney ";
            case Payment::TYPE_QIWI_TERMINAL: return "Qiwi терминал";
            case Payment::TYPE_YANDEX_WALLET: return "Яндекс.Деньги Wallet";
            case Payment::TYPE_COUPON: return "Купон";
            case Payment::TYPE_ANDROID: return "Android";
            case Payment::TYPE_TESTAPPLE: return "Apple Test";
            case Payment::TYPE_APPLE: return "Apple";
        }
        return "";
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'payments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sum', 'amount'], 'number'],
            [['user_id', 'type_id', 'site_id'], 'integer'],
            [['tm'], 'safe'],
            [['operation_id', 'operation_label', 'source'], 'string', 'max' => 255],
        ];
    }

    public function getSite()
    {
        return $this->hasOne(Site::className(), ["id" => "site_id"]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                => 'ID',
            'sum'               => 'Сумма',
            'amount'            => 'Зачисленно',
            'user_id'           => 'Пользователь',
            'tm'                => 'Дата и время',
            'operation_id'      => 'Транзакция',
            'operation_label'   => 'Комментарий',
            'site_id'           => 'Сайт'
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
