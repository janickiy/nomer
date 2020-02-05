<?php

use yii\db\Migration;

class m170301_110603_add_is_admin_field extends Migration
{
    public function up()
    {
        $this->addColumn("users", "is_admin", $this->boolean()->defaultValue(false));
    }

    public function down()
    {
        $this->dropColumn("users", "is_admin");
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
