<?php

use yii\db\Migration;

class m170731_154223_add_ref_balance_to_user extends Migration
{
    public function safeUp()
    {
        $this->addColumn("users", "ref_balance", $this->decimal(24, 4)->defaultValue(0));
    }

    public function safeDown()
    {
        $this->dropColumn("users", "ref_balance");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170731_154223_add_ref_balance_to_user cannot be reverted.\n";

        return false;
    }
    */
}
