<?php

use yii\db\Migration;

class m170413_142306_add_payed_field extends Migration
{
    public function up()
    {
        $this->addColumn("requests", "is_payed", $this->smallInteger()->defaultValue(0));
    }

    public function down()
    {
        $this->dropColumn("requests", "is_payed");
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
