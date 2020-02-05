<?php

use yii\db\Migration;

/**
 * Handles the creation of table `geo`.
 */
class m170529_130558_create_geo_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('geo', [
            'geoname_id' => $this->integer(),
            'locale_code' => $this->string(),
            'continent_code' => $this->string(),
            'continent_name' => $this->string(),
            'country_iso_code' => $this->string(),
            'country_name' => $this->string(),
            'subdivision_1_iso_code' => $this->string(),
            'subdivision_1_name' => $this->string(),
            'subdivision_2_iso_code' => $this->string(),
            'subdivision_2_name' => $this->string(),
            'city_name' => $this->string(),
            'metro_code' => $this->string(),
            'time_zone' => $this->string(),
        ]);

        $this->createIndex("idx_geo_id", "geo", "geoname_id");
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropIndex("idx_geo_id", "geo");

        $this->dropTable('geo');
    }
}
