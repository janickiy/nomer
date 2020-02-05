<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_fingerprints".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $ip
 * @property string $hash
 * @property string $tm
 * @property User $user
 */
class UserFingerprint extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_fingerprints';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['tm'], 'safe'],
            [['ip', 'hash'], 'string', 'max' => 255],
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
            'ip' => 'IP',
            'hash' => 'Hash',
            'tm' => 'Tm',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
