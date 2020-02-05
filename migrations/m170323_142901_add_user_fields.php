<?php

use yii\db\Migration;

class m170323_142901_add_user_fields extends Migration
{
    public function up()
    {
        $this->addColumn("users", "is_test", $this->boolean()->defaultValue(false));
        $this->addColumn("users", "checks", $this->integer()->defaultValue(0));
        $this->addColumn("users", "plan", $this->smallInteger()->defaultValue(0));
    }

    public function down()
    {
        $this->dropColumn("users", "plan");
        $this->dropColumn("users", "checks");
        $this->dropColumn("users", "is_test");
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
