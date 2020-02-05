<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "reposts".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $vk_id
 * @property string $tm
 * @property integer $status
 * @property integer $site_id
 * @property integer $sms_count
 */
class Repost extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'reposts';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'vk_id', 'status', 'site_id', 'sms_count'], 'integer'],
            [['tm'], 'safe'],
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
            'vk_id' => 'Профиль',
            'tm' => 'Дата',
            'status' => 'Статус',
            'sms_count' => 'Кол-во смс'
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ["id" => "user_id"]);
    }
}
