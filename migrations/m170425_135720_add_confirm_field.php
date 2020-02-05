<?php

use yii\db\Migration;

class m170425_135720_add_confirm_field extends Migration
{
    public function up()
    {
        $this->addColumn("users", "is_confirm", $this->boolean()->defaultValue(false));
        $this->addColumn("users", "tm_confirm", $this->dateTime());
    }

    public function down()
    {
        $this->dropColumn("users", "tm_confirm");
        $this->dropColumn("users", "is_confirm");
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
