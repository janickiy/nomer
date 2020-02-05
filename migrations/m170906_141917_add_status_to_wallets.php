<?php

use yii\db\Migration;

class m170906_141917_add_status_to_wallets extends Migration
{
    public function safeUp()
    {
        $this->addColumn(\app\models\Wallet::tableName(), "status", $this->boolean()->defaultValue(true));
    }

    public function safeDown()
    {
        $this->dropColumn(\app\models\Wallet::tableName(), "status");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170906_141917_add_status_to_wallets cannot be reverted.\n";

        return false;
    }
    */
}
