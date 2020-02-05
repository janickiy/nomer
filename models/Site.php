<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sites".
 *
 * @property integer $id
 * @property string $name
 * @property string $vk_id
 * @property string $vk_secret
 * @property string $fb_id
 * @property string $fb_secret
 * @property string $gg_id
 * @property string $gg_secret
 * @property boolean $is_demo
 * @property string $phone
 * @property string $yandex_money_account
 * @property integer $platiru_id
 * @property integer $type_id
 */
class Site extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sites';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['is_demo'], 'boolean'],
            ['name', 'unique'],
            [['platiru_id', 'type_id'], 'integer'],
            [
                [
                    'name',
                    'vk_id', 'vk_secret',
                    'fb_id', 'fb_secret',
                    'gg_id', 'gg_secret',
                    'phone',
                    'yandex_money_account',
                    'comment'
                ], 'string', 'max' => 255
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'        => 'ID',
            'name'      => 'Домен',
            'vk_id'     => 'VK ID приложения',
            'vk_secret' => 'VK секретный ключ',
            'fb_id'     => 'FB ID приложения',
            'fb_secret' => 'FB секретный ключ',
            'gg_id'     => 'Gg ID',
            'gg_secret' => 'Gg Secret',
            'is_demo'   => 'Демо режим',
            'phone'     => 'Номер телефона',
            'comment'   => 'Комментарий',
            'yandex_money_account' => 'Яндекс.Деньги',
            'platiru_id' => 'plati.ru - № товара',
            'type_id'   => 'Тип поиска'
        ];
    }
}
