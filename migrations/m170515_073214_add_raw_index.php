<?php

use yii\db\Migration;

class m170515_073214_add_raw_index extends Migration
{
    public function up()
    {
        $this->createIndex("idx_vk_raw", "vk_raw", "id");
    }

    public function down()
    {
        $this->dropIndex("idx_vk_raw", "vk_raw");
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
