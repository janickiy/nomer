<?php

namespace app\models;

use Yii;
use DateTime;

class Retargeting extends \yii\db\ActiveRecord
{
    const STATUS_QUEUE = 0;
    const STATUS_SENT = 1;
    const STATUS_READ = 2;
    const STATUS_CLICK = 3;
    const STATUS_ERROR = 4;

    /**
     * @param $status
     * @return null|string
     * получаем статус отправики писем
     */
    public static function getStatusName($status) {
        switch ($status) {
            case self::STATUS_QUEUE: return 'Письмо в очереди на отправку';
            case self::STATUS_SENT: return 'Письмо отправлено';
            case self::STATUS_READ: return 'Письмо прочитано';
            case self::STATUS_CLICK: return 'По ссылке был сделан переход';
            case self::STATUS_ERROR: return 'Ошибка отправки';
            default: return null;
        }
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'email_tokents';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'status'], 'integer'],
            [['uuid'], 'string', 'max' => 255],
            [['tm_create', 'tm_send', 'tm_read', 'tm_click'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uuid' => 'Uiid',
            'tm_create' => 'Дата создания',
            'tm_send' => 'Дата отправики',
            'tm_read' => 'Время прочтения',
            'tm_click' => 'Время перехода',
            'status' => 'Статус',
            'descr' => 'Описание',
        ];
    }

    /**
     * @param $date1
     * @param $date2
     * @return string
     * @throws \Exception
     * генератор случайных дат в временом промежутке
     */
    public static function random_date_in_range( $date1, $date2 )
    {
        if (!is_a($date1, 'DateTime')) {
            $date1 = new DateTime( (ctype_digit((string)$date1) ? '@' : '') . $date1);
            $date2 = new DateTime( (ctype_digit((string)$date2) ? '@' : '') . $date2);
        }

        $random_u = random_int($date1->format('U'), $date2->format('U'));
        $random_date = new DateTime();
        $random_date->setTimestamp($random_u);

        return $random_date->format('Y-m-d H:i');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ["id" => "user_id"]);
    }
}