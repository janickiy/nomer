<?php

use yii\db\Migration;

class m170330_092131_add_indexes extends Migration
{
    public function up()
    {
        $this->createIndex("idx_vk_phone1", "vk", "phone1");
        $this->createIndex("idx_vk_phone2", "vk", "phone2");

        $this->createIndex("idx_requests", "requests", ["user_id", "phone"]);
        $this->createIndex("idx_request_results_id", "request_results", "request_id");
        $this->createIndex("idx_cache", "cache", ["phone", "type_id"]);
    }

    public function down()
    {
        //$this->dropIndex("idx_cache", "cache");
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
