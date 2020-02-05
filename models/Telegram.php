<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property integer $id
 * @property string $host
 * @property integer $port
 * @property integer $status
 * @property string $tm_last
 */
class Telegram extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE  = 1;
    const STATUS_UNAVAILABLE = 2;

    public static function getStatusName($status) {
        switch ($status) {
            case self::STATUS_INACTIVE: return 'Неактивный';
            case self::STATUS_ACTIVE: return 'Активный';
            case self::STATUS_UNAVAILABLE: return 'Сервер недоступен';
            default: return null;
        }
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'telegrams';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['host', 'port'], 'required'],
            [['port', 'status'], 'integer'],
            [['tm_last'], 'safe'],
            [['host'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'host' => 'Host',
            'port' => 'Port',
            'status' => 'Status',
            'tm_last' => 'Tm Last',
        ];
    }
}
