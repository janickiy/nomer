<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "calls".
 *
 * @property integer $id
 * @property string $tm
 * @property string $cuid
 * @property integer $duration
 * @property string $status
 * @property integer $phone
 * @property Organization $organization
 */
class Call extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calls';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tm'], 'safe'],
            [['duration', 'phone'], 'integer'],
            [['cuid', 'status'], 'string', 'max' => 255],
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
            'cuid' => 'Cuid',
            'duration' => 'Duration',
            'status' => 'Status',
            'phone' => 'Phone',
        ];
    }

    public function getOrganization()
    {
        return $this->hasOne(Organization::className(), ['id' => 'org_id'])
            ->viaTable(OrganizationPhone::tableName(), ['phone2' => 'phone']);
    }
}
