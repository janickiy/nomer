<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "apple_payments".
 *
 * @property integer $id
 * @property string $tm
 * @property string $sum
 * @property string $amount
 * @property string $refund
 */
class ApplePayment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'apple_payments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tm'], 'safe'],
            [['sum', 'amount', 'refund'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tm' => 'Tm',
            'sum' => 'Sum',
            'amount' => 'Amount',
            'refund' => 'Refund',
        ];
    }
}