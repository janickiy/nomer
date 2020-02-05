<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "orgs".
 *
 * @property integer $id
 * @property string $name
 * @property string $date
 * @property string $maximum_sum
 * @property string $inn
 * @property string $number
 * @property string $region
 * @property OrganizationPhone[] $phones
 * @property OrganizationEmail[] $emails
 */
class Organization extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'orgs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['maximum_sum'], 'number'],
            [['name', 'inn', 'number', 'region'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'maximum_sum' => 'Maximum Sum',
            'inn' => 'Inn',
            'number' => 'Number',
            'region' => 'Region',
        ];
    }

    public function getPhones()
    {
        return $this->hasMany(OrganizationPhone::className(), ['org_id' => 'id']);
    }

    public function getEmails()
    {
        return $this->hasMany(OrganizationEmail::className(), ['org_id' => 'id']);
    }
}
