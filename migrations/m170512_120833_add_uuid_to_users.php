<?php

use yii\db\Migration;

class m170512_120833_add_uuid_to_users extends Migration
{
    public function up()
    {
        $this->addColumn('users', 'uuid', $this->string(64));
    }

    public function down()
    {
        $this->dropColumn('users', 'uuid');
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
