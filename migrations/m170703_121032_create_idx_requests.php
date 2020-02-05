<?php

use yii\db\Migration;

class m170703_121032_create_idx_requests extends Migration
{
    public function up()
    {
        $this->createIndex("idx_requets_tm", \app\models\SearchRequest::tableName(), "tm");
    }

    public function down()
    {

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
