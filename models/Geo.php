<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "geo".
 *
 * @property integer $geoname_id
 * @property string $locale_code
 * @property string $continent_code
 * @property string $continent_name
 * @property string $country_iso_code
 * @property string $country_name
 * @property string $subdivision_1_iso_code
 * @property string $subdivision_1_name
 * @property string $subdivision_2_iso_code
 * @property string $subdivision_2_name
 * @property string $city_name
 * @property string $metro_code
 * @property string $time_zone
 */
class Geo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'geo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['geoname_id'], 'integer'],
            [['locale_code', 'continent_code', 'continent_name', 'country_iso_code', 'country_name', 'subdivision_1_iso_code', 'subdivision_1_name', 'subdivision_2_iso_code', 'subdivision_2_name', 'city_name', 'metro_code', 'time_zone'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'geoname_id' => 'Geoname ID',
            'locale_code' => 'Locale Code',
            'continent_code' => 'Continent Code',
            'continent_name' => 'Continent Name',
            'country_iso_code' => 'Country Iso Code',
            'country_name' => 'Country Name',
            'subdivision_1_iso_code' => 'Subdivision 1 Iso Code',
            'subdivision_1_name' => 'Subdivision 1 Name',
            'subdivision_2_iso_code' => 'Subdivision 2 Iso Code',
            'subdivision_2_name' => 'Subdivision 2 Name',
            'city_name' => 'City Name',
            'metro_code' => 'Metro Code',
            'time_zone' => 'Time Zone',
        ];
    }
}
