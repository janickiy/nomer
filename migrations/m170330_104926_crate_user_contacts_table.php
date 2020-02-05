<?php

use yii\db\Migration;

class m170330_104926_crate_user_contacts_table extends Migration
{
    public function up()
    {
        $this->createTable('user_contacts', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'name' => $this->string(),
            'phone' => $this->string(),
            'tm' => $this->timestamp(),
            'last_check' => $this->timestamp()->defaultValue(null)
        ]);
    }

    public function down()
    {
        $this->dropTable('user_contacts');
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
