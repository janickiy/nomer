<?php

use yii\db\Migration;

class m170414_122946_add_result_index extends Migration
{
    public function up()
    {
        $this->createIndex("idx_request_result_request_id", "request_results", "request_id");
    }

    public function down()
    {
        echo "m170414_122946_add_result_index cannot be reverted.\n";

        return false;
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
