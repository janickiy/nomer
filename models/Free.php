<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "free".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $checks
 * @property string $uuid
 * @property integer $type_id
 * @property string $tm
 */
class Free extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'free';
    }

    const TYPE_INSTALL = 7781;
    const TYPE_RATE = 3902;

    static function types() {
        return [self::TYPE_INSTALL, self::TYPE_RATE];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'checks', 'type_id'], 'integer'],
            [['tm'], 'safe'],
            [['uuid'], 'string', 'max' => 255],
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
            'checks' => 'Checks',
            'uuid' => 'Uuid',
            'type_id' => 'Type ID',
            'tm' => 'Tm',
        ];
    }
}
