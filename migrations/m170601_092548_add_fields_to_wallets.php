<?php

use yii\db\Migration;

class m170601_092548_add_fields_to_wallets extends Migration
{
    public function up()
    {
        $this->addColumn(\app\models\Wallet::tableName(), "tm_last_transaction_out", $this->timestamp());
    }

    public function down()
    {
        echo "m170601_092548_add_fields_to_wallets cannot be reverted.\n";

        return false;
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
