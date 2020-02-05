<?php

use yii\db\Migration;

class m170824_164534_add_is_deleted_to_tickets extends Migration
{
    public function safeUp()
    {
        $this->addColumn(\app\models\Ticket::tableName(), "is_deleted", $this->boolean()->defaultValue(false));
        $this->addColumn(\app\models\TicketComment::tableName(), "is_deleted", $this->boolean()->defaultValue(false));
    }

    public function safeDown()
    {
        echo "m170824_164534_add_is_deleted_to_tickets cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170824_164534_add_is_deleted_to_tickets cannot be reverted.\n";

        return false;
    }
    */
}
