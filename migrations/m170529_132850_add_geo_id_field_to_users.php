<?php

use yii\db\Migration;

class m170529_132850_add_geo_id_field_to_users extends Migration
{
    public function up()
    {
        $this->addColumn('users', 'geo_id', $this->integer());
    }

    public function down()
    {
        $this->dropColumn('users', 'geo_id');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
