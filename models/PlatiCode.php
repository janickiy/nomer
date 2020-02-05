<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "plati_codes".
 *
 * @property integer $id
 * @property string $code
 * @property integer $checks
 * @property integer $user_id
 * @property string $tm_create
 * @property string $tm_used
 */
class PlatiCode extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'plati_codes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['checks', 'user_id'], 'integer'],
            [['tm_create', 'tm_used'], 'safe'],
            [['code'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'checks' => 'Checks',
            'user_id' => 'User ID',
            'tm_create' => 'Tm Create',
            'tm_used' => 'Tm Used',
        ];
    }
}
