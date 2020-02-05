<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "vk".
 *
 * @property integer $id
 * @property string $www
 * @property string $skype
 * @property string $instagram
 * @property string $twitter
 * @property string $facebook
 * @property string $phone1
 * @property string $phone2
 * @property string $phone3
 * @property string $phone4
 */
class Vk extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vk';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['www', 'skype', 'instagram', 'twitter', 'facebook', 'phone1', 'phone2', 'phone3', 'phone4'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'www' => 'Www',
            'skype' => 'Skype',
            'instagram' => 'Instagram',
            'twitter' => 'Twitter',
            'facebook' => 'Facebook',
            'phone1' => 'Phone1',
            'phone2' => 'Phone2',
            'phone3' => 'Phone3',
            'phone4' => 'Phone4',
        ];
    }
}
