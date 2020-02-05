<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "notifications".
 *
 * @property integer $id
 * @property mixed tm_send
 * @property mixed tm_create
 * @property mixed payload
 * @property mixed message
 */
class Notification extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'notifications';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['message', 'payload', 'tm_create', 'tm_send'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'message'   => 'Сообщение',
            'payload'   => 'payload',
            'tm_create' => 'Дата создания',
            'tm_send'   => 'Дата отправки'
        ];
    }

    public function getResults()
    {
        return $this->hasMany(NotificationResult::className(), ["notify_id" => "id"]);
    }

    public function getGoodResults()
    {
        return $this->hasMany(NotificationResult::className(), ["notify_id" => "id"])->where(["status" => 200]);
    }
}
