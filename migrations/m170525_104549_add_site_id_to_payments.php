<?php

use yii\db\Migration;

class m170525_104549_add_site_id_to_payments extends Migration
{
    public function up()
    {
        $this->addColumn(\app\models\WebmoneyOrder::tableName(), "site_id", $this->smallInteger());
        $this->addColumn(\app\models\Payment::tableName(), "site_id", $this->smallInteger());
    }

    public function down()
    {
        echo "m170525_104549_add_site_id_to_payments cannot be reverted.\n";

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
