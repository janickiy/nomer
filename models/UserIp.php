<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_ips".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $ip
 * @property string $tm
 */
class UserIp extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_ips';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['tm'], 'safe'],
            [['ip'], 'string', 'max' => 255],
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
            'ip' => 'Ip',
            'tm' => 'Tm',
        ];
    }
}
