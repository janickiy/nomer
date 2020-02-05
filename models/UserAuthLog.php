<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_auth_log".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $site_id
 * @property string $ip
 * @property string $tm
 * @property Site $site
 */
class UserAuthLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_auth_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'site_id'], 'integer'],
            [['tm'], 'safe'],
            [['ip'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'site_id' => 'Сайт',
            'ip' => 'IP',
            'tm' => 'Время',
        ];
    }

    public function getSite()
    {
        return $this->hasOne(Site::className(), ['id' => 'site_id']);
    }
}
