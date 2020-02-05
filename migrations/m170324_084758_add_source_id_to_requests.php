<?php

use yii\db\Migration;
use app\models\SearchRequest;

class m170324_084758_add_source_id_to_requests extends Migration
{
    public function up()
    {
        $this->addColumn(SearchRequest::tableName(), 'source_id', $this->smallInteger());
    }

    public function down()
    {
        $this->dropColumn(SearchRequest::tableName(), 'source_id');
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
