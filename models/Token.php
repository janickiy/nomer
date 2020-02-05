<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property integer $id
 * @property integer $type
 * @property integer $server_id
 * @property string $token
 * @property integer $status
 * @property string $tm_ban
 */
class Token extends ActiveRecord
{
    const TYPE_TRUECALLER = 1;

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    public static function getTypeName($type) {
        switch ($type) {
            case self::TYPE_TRUECALLER: return 'Truecaller';
            default: return null;
        }
    }

    public static function getTypes()
    {
        return [
            self::TYPE_TRUECALLER => self::getTypeName(self::TYPE_TRUECALLER)
        ];
    }

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
        return 'tokens';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'token'], 'required'],
            ['server_id', 'integer'],
            ['server_id', 'default', 'value' => 0],
            ['status', 'in', 'range' => array_keys(self::getTypes())],
            ['tm_ban', 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Тип',
            'server_id' => 'Сервер',
            'token' => 'Токен',
            'status' => 'Статус',
            'tm_ban' => 'Время блокировки'
        ];
    }
}
