<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "org_emails".
 *
 * @property integer $id
 * @property integer $org_id
 * @property string $name
 * @property string $email
 */
class OrganizationEmail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'org_emails';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['org_id'], 'integer'],
            [['name', 'email'], 'string', 'max' => 255],
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
            'email' => 'Email',
        ];
    }
}
