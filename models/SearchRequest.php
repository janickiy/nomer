<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "requests".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $phone
 * @property string $ip
 * @property string $ua
 * @property string $result
 * @property string $tm
 * @property boolean $refresh
 * @property int $source_id
 * @property RequestResult[] $results
 * @property \app\models\User $user
 * @property integer $is_payed
 * @property integer $site_id
 * @property Site $site
 * @property boolean $is_has_name
 * @property boolean $is_has_photo
 */
class SearchRequest extends \yii\db\ActiveRecord
{
    const SOURCE_WEB = 1;
    const SOURCE_MOBILE = 2;
    const SOURCE_ANDROID = 3;
    const SOURCE_IOS = 4;
    const SOURCE_FCHECK = 5;
    const SOURCE_TELEGRAM = 6;

    public static function primaryKey()
    {
        return ["id"];
    }

    public static function getSourceText($id)
    {
        switch ($id) {
            case self::SOURCE_WEB:
                return 'Web';
            case self::SOURCE_MOBILE:
                return 'Mobile';
            case self::SOURCE_ANDROID:
                return 'Android';
            case self::SOURCE_IOS:
                return 'iOS';
            case self::SOURCE_FCHECK:
                return 'FCHECK';
        }
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'requests';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'is_payed', 'site_id', 'source_id'], 'integer'],
            [['result'], 'string'],
            [['tm'], 'safe'],
            [['refresh', 'is_has_name', 'is_has_photo'], 'boolean'],
            [['phone', 'ip', 'ua'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Пользователь',
            'phone' => 'Телефон',
            'ip' => 'IP',
            'ua' => 'Ua',
            'result' => 'Result',
            'tm' => 'Время поиска',
            'refresh' => 'Refresh',
            'site_id' => 'Сайт',
            'source_id' => 'Источник'
        ];
    }

    public function getResults()
    {
        return $this->hasMany(RequestResult::className(), ['request_id' => 'id'])->select(['request_id', 'type_id', 'index', 'cache_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getSite()
    {
        return $this->hasOne(Site::className(), ['id' => 'site_id']);
    }
}
