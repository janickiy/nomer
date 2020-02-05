<?php

use yii\db\Migration;

class m170606_153714_add_reposts_table extends Migration
{
    public function up()
    {
        $this->createTable("reposts", [
            "id"        => $this->primaryKey(),
            "user_id"   => $this->integer(),
            "site_id"   => $this->smallInteger(),
            "vk_id"     => $this->bigInteger(),
            "tm"        => $this->timestamp(),
            "status"    => $this->smallInteger()->defaultValue(1)
        ]);
    }

    public function down()
    {
        $this->dropTable("reposts");
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
