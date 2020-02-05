<?php

use yii\db\Migration;

class m170630_165222_add_site_id_field_to_wallets extends Migration
{
    public function safeUp()
    {
        $this->addColumn('wallets', 'site_id', $this->integer());
    }

    public function safeDown()
    {
        $this->dropColumn('wallets', 'site_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170630_165222_add_site_id_field_to_wallets cannot be reverted.\n";

        return false;
    }
    */
}
