<?php

use yii\db\Migration;

class m170404_123244_add_user_ip extends Migration
{
    public function up()
    {
        $this->addColumn("users", "ip", $this->string());
    }

    public function down()
    {
        $this->dropColumn("users", "ip");
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
