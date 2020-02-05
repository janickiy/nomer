<?php

namespace app\models;

use Yii;

class EmailTemplates extends \yii\db\ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE  = 1;

    public static function getStatusName($status) {
        switch ($status) {
            case self::STATUS_INACTIVE: return 'Неактивный';
            case self::STATUS_ACTIVE: return 'Активный';
            default: return null;
        }
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'email_templates';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['subject', 'message'], 'required'],
            [['status'], 'integer'],
            [['tm_create'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'subject' => 'Тема',
            'message' => 'Сообщение',
            'status' => 'Статус',
            'tm_create' => 'Создан',
        ];
    }
}
