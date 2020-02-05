<?php

use yii\db\Migration;

class m170327_115426_add_status_to_user extends Migration
{
    public function up()
    {
        $this->addColumn("users", "status", $this->smallInteger()->defaultValue(1));
    }

    public function down()
    {
        $this->dropColumn("users", "status");
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
