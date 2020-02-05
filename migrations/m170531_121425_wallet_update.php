<?php

use yii\db\Migration;
use app\models\Wallet;

class m170531_121425_wallet_update extends Migration
{
    public function up()
    {
        $this->addColumn(Wallet::tableName(), "login", $this->string());
        $this->alterColumn(Wallet::tableName(), "balance", $this->decimal(24, 4));

       

    }

    public function down()
    {
        $this->dropColumn(Wallet::tableName(), "login");
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
