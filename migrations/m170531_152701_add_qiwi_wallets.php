<?php

use app\models\Wallet;
use yii\db\Migration;

class m170531_152701_add_qiwi_wallets extends Migration
{
    public function up()
    {
        $this->insert(Wallet::tableName(), [
            "type_id"   => Wallet::TYPE_QIWI,
            "wallet_id" => "+79296436441",
            "login"     => "79296436441",
            "password"  => "Ag6K2oxG"
        ]);
        $this->insert(Wallet::tableName(), [
            "type_id"   => Wallet::TYPE_QIWI,
            "wallet_id" => "+79295595315",
            "login"     => "79295595315",
            "password"  => "Ag6K2oxG"
        ]);
        $this->insert(Wallet::tableName(), [
            "type_id"   => Wallet::TYPE_QIWI,
            "wallet_id" => "+79295595275",
            "login"     => "79295595275",
            "password"  => "Ag6K2oxG"
        ]);
        $this->insert(Wallet::tableName(), [
            "type_id"   => Wallet::TYPE_QIWI,
            "wallet_id" => "+79295595634",
            "login"     => "79295595634",
            "password"  => "Ag6K2oxG"
        ]);
        $this->insert(Wallet::tableName(), [
            "type_id"   => Wallet::TYPE_QIWI,
            "wallet_id" => "+79295595705",
            "login"     => "79295595705",
            "password"  => "Ag6K2oxG"
        ]);
        $this->insert(Wallet::tableName(), [
            "type_id"   => Wallet::TYPE_QIWI,
            "wallet_id" => "+79295595495",
            "login"     => "79295595495",
            "password"  => "Ag6K2oxG"
        ]);
        $this->insert(Wallet::tableName(), [
            "type_id"   => Wallet::TYPE_QIWI,
            "wallet_id" => "+79295595285",
            "login"     => "79295595285",
            "password"  => "Ag6K2oxG"
        ]);
        $this->insert(Wallet::tableName(), [
            "type_id"   => Wallet::TYPE_QIWI,
            "wallet_id" => "+79295595725",
            "login"     => "79295595725",
            "password"  => "Ag6K2oxG"
        ]);
        $this->insert(Wallet::tableName(), [
            "type_id"   => Wallet::TYPE_QIWI,
            "wallet_id" => "+79295595815",
            "login"     => "79295595815",
            "password"  => "Ag6K2oxG"
        ]);
        $this->insert(Wallet::tableName(), [
            "type_id"   => Wallet::TYPE_QIWI,
            "wallet_id" => "+79295595206",
            "login"     => "79295595206",
            "password"  => "Ag6K2oxG"
        ]);
        $this->insert(Wallet::tableName(), [
            "type_id"   => Wallet::TYPE_QIWI,
            "wallet_id" => "+79269516206",
            "login"     => "79269516206",
            "password"  => "Ag6K2oxG"
        ]);
    }

    public function down()
    {
        echo "m170531_152701_add_qiwi_wallets cannot be reverted.\n";

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
