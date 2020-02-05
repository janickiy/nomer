<?php
namespace app\models;


/**
 * This is the model class for table "webmoney_orders".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $tm_create
 * @property string $sum
 * @property integer $status
 * @property integer $site_id
 */
class WebmoneyOrder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'webmoney_orders';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'status', 'site_id'], 'integer'],
            [['tm_create'], 'safe'],
            [['sum'], 'number']
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
            'tm_create' => 'Tm Create',
            'sum' => 'Sum',
            'status' => 'Status',
        ];
    }
}
