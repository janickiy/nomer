<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "checkouts".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $wallet
 * @property string $sum
 * @property string $tm_create
 * @property string $tm_done
 * @property integer $status
 */
class Checkout extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'checkouts';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'status'], 'integer'],
            [['sum'], 'number'],
            [['tm_create', 'tm_done'], 'safe'],
            [['wallet'], 'string', 'max' => 255],
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
            'wallet' => 'Wallet',
            'sum' => 'Sum',
            'tm_create' => 'Tm Create',
            'tm_done' => 'Tm Done',
            'status' => 'Status',
        ];
    }
}
