<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ticket_replies".
 *
 * @property integer $id
 * @property integer $subject_id
 * @property string $text
 */
class TicketReply extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ticket_replies';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['subject_id'], 'integer'],
            [['text'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'subject_id' => 'Subject ID',
            'text' => 'Text',
        ];
    }
}
