<?php

namespace app\models\forms;

use yii\base\Model;

class AdminHistoryFilterForm extends Model
{
    /**
     * @var string
     */
    public $from;

    /**
     * @var string
     */
    public $to;

    /**
     * @var int
     */
    public $user;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['from', 'to'], 'required'],
            ['user', 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'from' => 'От',
            'to' => 'До'
        ];
    }
}
