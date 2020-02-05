<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "facebook".
 *
 * @property integer $id
 * @property string $tm
 * @property string $phone
 * @property string $fb_id
 * @property string $name
 * @property string $photo
 */
class Facebook extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'facebook';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tm'], 'safe'],
            [['photo'], 'string'],
            [['phone', 'fb_id', 'name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tm' => 'Tm',
            'phone' => 'Phone',
            'fb_id' => 'Fb ID',
            'name' => 'Name',
            'photo' => 'Photo',
        ];
    }
}
