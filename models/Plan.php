<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "plans".
 *
 * @property integer $id
 * @property integer $cost
 * @property integer $count
 * @property string $title
 * @property boolean $status
 */
class Plan extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'plans';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cost', 'count'], 'integer'],
            [['status'], 'boolean'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cost' => 'Cost',
            'count' => 'Count',
            'title' => 'Title',
            'status' => 'Status',
        ];
    }
}
