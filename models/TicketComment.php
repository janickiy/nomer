<?php

namespace app\models;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "ticket_comments".
 *
 * @property integer $id
 * @property string $ticket_id
 * @property integer $user_id
 * @property string $text
 * @property string $tm_create
 * @property string $tm_read
 * @property integer $type_id
 * @property integer $is_deleted
 */
class TicketComment extends \yii\db\ActiveRecord
{

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['tm_create'],
                ],
                'value' => new Expression('NOW()'),
            ],
            'user_id' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['user_id'],
                ],
                'value' => Yii::$app->request->isConsoleRequest?"":\Yii::$app->getUser()->getId(),
            ],
            'type_id' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['user_id'],
                ],
                'value' => \Yii::$app->getUser()->getIdentity()->is_admin?1:2,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ticket_comments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'ticket_id', 'type_id', 'is_deleted'], 'integer'],
            [['text'], 'string', 'min' => 3],
            [['text'], 'required'],
            [['tm_create', 'tm_read'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ticket_id' => 'Запрос',
            'user_id' => 'Пользователь',
            'text' => 'Ответ',
            'tm_create' => 'Дата создания',
            'tm_read' => 'Дата прочтения',
        ];
    }

    public function getUser() {
        return $this->hasOne(User::className(), ["id" => "user_id"]);
    }

}
