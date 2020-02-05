<?php

use yii\db\Migration;

class m170405_142656_add_ban_column extends Migration
{
    public function up()
    {
        $this->addColumn("users", "ban", $this->smallInteger()->defaultValue(0));
    }

    public function down()
    {
        $this->dropColumn("users", "ban");
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
