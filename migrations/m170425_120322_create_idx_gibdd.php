<?php

use yii\db\Migration;

class m170425_120322_create_idx_gibdd extends Migration
{
    public function up()
    {
        $this->createIndex("idx_gibdd_phone", "gibdd", "phone");
    }

    public function down()
    {
        $this->dropIndex("idx_gibdd_phone", "gibdd");
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
