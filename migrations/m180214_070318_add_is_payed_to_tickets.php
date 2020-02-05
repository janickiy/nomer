<?php

use yii\db\Migration;

class m180214_070318_add_is_payed_to_tickets extends Migration
{
    public function safeUp()
    {
        $this->addColumn(\app\models\Ticket::tableName(), "is_payed", $this->boolean()->defaultValue(false));
    }

    public function safeDown()
    {

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180214_070318_add_is_payed_to_tickets cannot be reverted.\n";

        return false;
    }
    */
}