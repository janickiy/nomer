<?php

use yii\db\Migration;
use app\models\Wallet;

class m170531_121425_wallet_update extends Migration
{
    public function up()
    {
        $this->addColumn(Wallet::tableName(), "login", $this->string());
        $this->alterColumn(Wallet::tableName(), "balance", $this->decimal(24, 4));

        $this->insert(Wallet::tableName(), [
            "type_id" => Wallet::TYPE_YANDEX,
            "wallet_id" => "410014057045840",
            "login" => "mezhevikina.masha@yandex.ru",
            "password" => "Ag6K2oxG"
        ]);
        $this->insert(Wallet::tableName(), [
            "type_id" => Wallet::TYPE_YANDEX,
            "wallet_id" => "410014074158389",
            "login" => "tarasov.pavel.2018@yandex.ru",
            "password" => "Ag6K2oxG"
        ]);
        $this->insert(Wallet::tableName(), [
            "type_id" => Wallet::TYPE_YANDEX,
            "wallet_id" => "410015251188084",
            "login" => "evgeniy.sokolov.2018@yandex.ru",
            "password" => "Ag6K2oxG"
        ]);
        $this->insert(Wallet::tableName(), [
            "type_id" => Wallet::TYPE_YANDEX,
            "wallet_id" => "410015251417595",
            "login" => "bistrov.anton.2018@yandex.ru",
            "password" => "Ag6K2oxG"
        ]);
        $this->insert(Wallet::tableName(), [
            "type_id" => Wallet::TYPE_YANDEX,
            "wallet_id" => "410015251454465",
            "login" => "kokorin.alexander.2018@yandex.ru",
            "password" => "Ag6K2oxG"
        ]);
        $this->insert(Wallet::tableName(), [
            "type_id" => Wallet::TYPE_YANDEX,
            "wallet_id" => "410015274661660",
            "login" => "efimov.ilya.2018@yandex.ru",
            "password" => "Ag6K2oxG"
        ]);
        $this->insert(Wallet::tableName(), [
            "type_id" => Wallet::TYPE_YANDEX,
            "wallet_id" => "410014080147590",
            "login" => "eliseenkova.elena.2018@yandex.ru",
            "password" => "Ag6K2oxG"
        ]);
        $this->insert(Wallet::tableName(), [
            "type_id" => Wallet::TYPE_YANDEX,
            "wallet_id" => "410015267023628",
            "login" => "avakova.karina.1994@yandex.ru",
            "password" => "Ag6K2oxG"
        ]);
        $this->insert(Wallet::tableName(), [
            "type_id" => Wallet::TYPE_YANDEX,
            "wallet_id" => "410015267675498",
            "login" => "petrusevitch.vladislav@yandex.ru",
            "password" => "Ag6K2oxG"
        ]);
        $this->insert(Wallet::tableName(), [
            "type_id" => Wallet::TYPE_YANDEX,
            "wallet_id" => "410014090959566",
            "login" => "alexandr.kowal2018@yandex.ru",
            "password" => "Ag6K2oxG"
        ]);

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
