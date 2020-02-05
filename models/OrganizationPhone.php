<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "org_phones".
 *
 * @property integer $id
 * @property integer $org_id
 * @property string $name
 * @property string $phone
 * @property string $phone2
 */
class OrganizationPhone extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'org_phones';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['org_id'], 'integer'],
            [['name', 'phone'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'org_id' => 'Org ID',
            'name' => 'Name',
            'phone' => 'Phone',
        ];
    }
}
