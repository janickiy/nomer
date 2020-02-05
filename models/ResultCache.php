<?php
namespace app\models;

/**
 * This is the model class for table "cache".
 *
 * @property integer $id
 * @property string $phone
 * @property string $data
 * @property integer $type_id
 * @property string $tm
 */
class ResultCache extends \yii\db\ActiveRecord
{

    const TYPE_OPERATOR = 1;
    const TYPE_BASIC = 2;
    const TYPE_FACEBOOK = 3;
    const TYPE_GOOGLE_PHONE = 4;
    const TYPE_AVITO = 5;
    const TYPE_VK = 6;
    const TYPE_GOOGLE_PHOTOS = 7;
    const TYPE_MAMBA = 8;
    const TYPE_BADOO = 9;
    const TYPE_VIBER = 10;
    const TYPE_AVINFO = 11;
    const TYPE_SPRUT = 12;
    const TYPE_TRUECALLER = 13;
    const TYPE_NUMBUSTER = 14;
    const TYPE_VK_2012 = 15;
    const TYPE_INSTAGRAM = 16;
    const TYPE_ANTIPARKON = 17;
    const TYPE_TELEGRAM = 18;
    const TYPE_AVINFO_API = 19;
    const TYPE_GIBDD = 20;
    const TYPE_VK_OPEN = 21;
    const TYPE_GETCONTACT = 22;
    const TYPE_SCORISTA = 23;

    public static function primaryKey()
    {
        return ["id"];
    }

    public static function getTypeSysname($id) {
        switch($id) {
            case 2: return "basic";
            case 3: return "facebook";
            case 5: return "avito";
            case 6: return "vk";
            case 15: return "vk_2012";
            case 18: return "telegram";
            case 10: return "viber";
            case 13: return "truecaller";
            case 14: return "numbuster";
            case 16: return "instagram";
            case 19: return "avinfo";
            case 17: return "avinfo";
            case 22: return "getcontact";
            case 23: return "scorista";
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cache';
    }

    public static function getTypeName($type_id)
    {
        switch ($type_id) {
            case self::TYPE_OPERATOR: return 'Оператор и регион';
            case self::TYPE_FACEBOOK: return 'Facebook';
            case self::TYPE_GOOGLE_PHONE: return 'Поиск в Google';
            case self::TYPE_GOOGLE_PHOTOS: return 'Поиск в google по фото';
            case self::TYPE_AVITO: return 'Avito';
            case self::TYPE_VK: return 'ВКонтакте';
            case self::TYPE_VK_2012: return 'ВКонтакте 2012';
            case self::TYPE_MAMBA: return 'Поиск на Мамбе';
            case self::TYPE_BADOO: return 'Поиск в Badoo';
            case self::TYPE_VIBER: return 'Viber';
            case self::TYPE_AVINFO: return 'Auto.ru';
            case self::TYPE_SPRUT: return 'Скориста';
            case self::TYPE_TRUECALLER: return 'Truecaller';
            case self::TYPE_NUMBUSTER: return 'Numbuster';
            case self::TYPE_INSTAGRAM: return 'Instagram';
            case self::TYPE_ANTIPARKON: return 'Антипаркон';
            case self::TYPE_TELEGRAM: return 'Telegram';
            case self::TYPE_AVINFO_API: return 'Avinfo API';
            case self::TYPE_GIBDD: return 'Гибдд';
            case self::TYPE_VK_OPEN: return 'Вконтакте открытые данные';
            case self::TYPE_GETCONTACT: return 'GetContact';
            case self::TYPE_SCORISTA: return 'Скориста';

            default: return null;
        }
    }

    public static function inactiveTypes()
    {
        return [
            ResultCache::TYPE_GOOGLE_PHONE,
            ResultCache::TYPE_GOOGLE_PHOTOS,
            ResultCache::TYPE_MAMBA,
            ResultCache::TYPE_BADOO,
            ResultCache::TYPE_AVINFO,
            ResultCache::TYPE_SPRUT
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['data'], 'string'],
            [['phone'], 'string', 'max' => 255],
            [['type_id'], 'integer'],
            [['tm'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'        => 'ID',
            'phone'     => 'Телефон',
            'data'      => 'Данные',
            'type_id'   => 'Тип',
            'tm'        => 'Дата/время'
        ];
    }
}
