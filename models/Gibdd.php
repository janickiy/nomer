<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "gibdd".
 *
 * @property string $tm
 * @property string $number
 * @property string $model
 * @property string $year
 * @property string $lastname
 * @property string $firstname
 * @property string $middlename
 * @property string $phone
 */
class Gibdd extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gibdd';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tm', 'number'], 'string', 'max' => 10],
            [['model'], 'string', 'max' => 64],
            [['year'], 'string', 'max' => 4],
            [['lastname', 'firstname', 'middlename'], 'string', 'max' => 32],
            [['phone'], 'string', 'max' => 11],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tm' => 'Tm',
            'number' => 'Number',
            'model' => 'Model',
            'year' => 'Year',
            'lastname' => 'Lastname',
            'firstname' => 'Firstname',
            'middlename' => 'Middlename',
            'phone' => 'Phone',
        ];
    }
}
