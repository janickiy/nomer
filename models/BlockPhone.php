<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "block".
 *
 * @property integer $id
 * @property string $phone
 * @property string $ip
 * @property string $ua
 * @property string $tm
 * @property string $code
 * @property integer $status
 * @property integer $site_id
 */
class BlockPhone extends \yii\db\ActiveRecord
{
    const STATUS_UNCONFIRMED = 0;
    const STATUS_CONFIRMED = 1;
    const STATUS_PAID = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'block';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tm'], 'safe'],
            [['status', 'site_id'], 'integer'],
            [['phone', 'ip', 'ua'], 'string', 'max' => 255],
            [['code'], 'string', 'max' => 4],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'phone' => 'Phone',
            'ip' => 'Ip',
            'ua' => 'Ua',
            'tm' => 'Tm',
            'code' => 'Code',
            'status' => 'Status',
        ];
    }
}
