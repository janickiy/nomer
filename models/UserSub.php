<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "subs".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $transaction_id
 * @property integer $original_transaction_id
 * @property string $tm_expires
 * @property integer status
 * @property string tm_purchase
 */
class UserSub extends \yii\db\ActiveRecord
{

    public static function primaryKey()
    {
        return ["id"];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'subs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'transaction_id', 'original_transaction_id', 'status'], 'integer'],
            [['tm_purchase', 'tm_expires'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Пользователь',
            'transaction_id' => 'Транзакция',
            'original_transaction_id' => 'Основная транзакция',
            'tm_purchase' => 'Дата подписки',
            'tm_expires' => 'Окончание подписки',
        ];
    }
}
