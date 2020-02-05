<?php

namespace app\models;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "tickets".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $ip
 * @property integer $subject_id
 * @property string $subject
 * @property string $text
 * @property integer $status
 * @property string $tm_create
 * @property string $tm_read
 * @property string $tm_close
 * @property string $tm_reopen
 * @property integer $is_deleted
 * @property string $url
 * @property int is_payed
 */
class Ticket extends \yii\db\ActiveRecord
{

    const SUBJECTS = [
        1 => 'Общие вопросы',
        2 => 'Оплата',
        3 => 'Сотрудничество',
        4 => 'Удаление номера'
    ];

    const STATUSES = [
        0 => 'Администратор пока не прочитал',
        1 => 'Администратор прочитал. Ждите ответ',
        2 => 'Администратор ответил. Ждём вашего прочтения',
        3 => 'Администратор ждет вашего ответа',
        4 => 'Закрыта',
        5 => 'Переоткрыта',
        6 => 'Игнорируемая задача',
        7 => 'Разработка'
    ];

    public $reCaptcha;

    public static function primaryKey()
    {
        return ["id"];
    }

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
            'ip' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ip'],
                ],
                'value' => Yii::$app->request->isConsoleRequest?"":\Yii::$app->request->getUserIP(),
            ],
            'user_id' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['user_id'],
                ],
                'value' => Yii::$app->request->isConsoleRequest?"":(\Yii::$app->getUser()->getId()),
            ],
            'status' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['status'],
                ],
                'value' => 0,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tickets';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['text', 'subject'], 'required'],
            [['text'], 'string', 'min' => 10],
            [['user_id', 'subject_id', 'status', 'site_id', 'is_deleted'], 'integer'],
            ['is_payed', 'boolean'],
            [['text'], 'string'],
            [['tm_create', 'tm_read', 'tm_close', 'tm_reopen'], 'safe'],
            [['ip'], 'string', 'max' => 15],
            [['subject', 'url'], 'string', 'max' => 255],
            [['reCaptcha'], \himiklab\yii2\recaptcha\ReCaptchaValidator::className(), 'secret' => '6LdpNCMUAAAAABTYWw_Eaca7iGlbXaCWWe0fqqp7', 'uncheckedMessage' => 'Пожалуйста, подтвержите, что вы не бот!']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'user_id'       => 'Пользователь',
            'ip'            => 'IP адрес',
            'subject_id'    => 'Раздел',
            'subject'       => 'Тема запроса',
            'text'          => 'Текст запроса',
            'status'        => 'Статус',
            'tm_create'     => 'Дата создания',
            'tm_read'       => 'Дата прочтения',
            'tm_close'      => 'Дата закрытия',
            'tm_reopen'     => 'Tm Reopen',
        ];
    }

    public function getUser() {
        return $this->hasOne(User::className(), ["id" => "user_id"]);
    }

    public function getSite() {
        return $this->hasOne(Site::className(), ["id" => "site_id"]);
    }

    public function getComments() {
        return $this->hasMany(TicketComment::className(), ["ticket_id" => "id"])->where(["is_deleted" => 0]);
    }
}
