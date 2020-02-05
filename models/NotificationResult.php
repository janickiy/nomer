<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "notification_results".
 *
 * @property integer $id
 * @property int notify_id
 * @property int user_id
 * @property int status
 *
 */
class NotificationResult extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'notification_results';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['notify_id', 'user_id', 'status'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
        ];
    }
}
