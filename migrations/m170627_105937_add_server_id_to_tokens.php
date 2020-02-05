<?php

use yii\db\Migration;

class m170627_105937_add_server_id_to_tokens extends Migration
{
    public function up()
    {
        $this->addColumn('tokens', 'server_id', $this->integer()->defaultValue(0));
    }

    public function down()
    {
        $this->dropColumn('tokens', 'server_id');
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
