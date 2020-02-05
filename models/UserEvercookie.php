<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_evercookies".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $ip
 * @property string $data
 * @property string $tm
 */
class UserEvercookie extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_evercookies';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['tm'], 'safe'],
            [['ip', 'data'], 'string', 'max' => 255],
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
            'data' => 'Data',
            'tm' => 'Tm',
        ];
    }
}
