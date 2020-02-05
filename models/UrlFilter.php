<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "urls".
 *
 * @property integer $id
 * @property string $url
 * @property integer $type
 */
class UrlFilter extends \yii\db\ActiveRecord
{
    const TYPE_BANNED = 1;
    const TYPE_TRUSTED = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'urls';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type'], 'integer'],
            [['url'], 'string', 'max' => 255],
            [['url', 'type'], 'required'],
            ['url', 'unique'],
            ['type', 'in', 'range' => [self::TYPE_BANNED, self::TYPE_TRUSTED]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'url' => 'URL',
            'type' => 'Тип',
        ];
    }

    /**
     * @param int $type
     * @return string
     * @throws \Exception
     */
    public static function typeText(int $type)
    {
        switch ($type) {
            case self::TYPE_BANNED:
                return 'Заблокированный';
            case self::TYPE_TRUSTED:
                return 'Доверенный';
            default:
                throw new \Exception('Unexpected UrlFilter type');
        }
    }

    public static function getTypes(): array {
        return [
          self::TYPE_BANNED => self::typeText(self::TYPE_BANNED),
          self::TYPE_TRUSTED => self::typeText(self::TYPE_TRUSTED)
        ];
    }
}
