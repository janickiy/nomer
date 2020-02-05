<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "wallets".
 *
 * @property integer $id
 * @property integer $type_id
 * @property string $wallet_id
 * @property string $login
 * @property string $password
 * @property double $balance
 * @property string $tm_last_balance
 * @property string $tm_last_transaction
 * @property string $tm_last_transaction_out
 * @property integer $site_id
 * @property Site $site
 * @property boolean $status
 */
class Wallet extends \yii\db\ActiveRecord
{
    const TYPE_YANDEX = 1;
    const TYPE_QIWI = 2;

    public static function getWalletTypes()
    {
        return [
            self::TYPE_YANDEX => 'Яндекс.Деньги',
            self::TYPE_QIWI => 'Qiwi'
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wallets';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type_id', 'wallet_id', 'login', 'password'], 'required'],
            [['type_id', 'site_id', 'status'], 'integer'],
            [['balance'], 'number'],
            [['tm_last_balance', 'tm_last_transaction', 'tm_last_transaction_out'], 'safe'],
            [['wallet_id', 'password', 'login'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 11],
            ['comment', 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type_id' => 'Тип',
            'site_id' => 'Сайт',
            'wallet_id' => 'Кошелек',
            'login' => 'Логин',
            'password' => 'Пароль',
            'balance' => 'Баланс',
            'tm_last_balance' => 'Дата получения баланса',
            'tm_last_transaction' => 'Приход',
            'tm_last_transaction_out' => 'Расход',
            'phone' => 'Номер телефона',
            'comment' => 'Комментарии',
            'status' => 'Статус'
        ];
    }

    public function getSite()
    {
        return $this->hasOne(Site::className(), ['id' => 'site_id']);
    }
}
