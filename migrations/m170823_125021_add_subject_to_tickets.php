<?php

use yii\db\Migration;
use \app\models\Ticket;

class m170823_125021_add_subject_to_tickets extends Migration
{
    public function safeUp()
    {
        $this->addColumn(Ticket::tableName(), 'subject', $this->string());
    }

    public function safeDown()
    {
        $this->dropColumn(Ticket::tableName(), 'subject');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170823_125021_add_subject_to_tickets cannot be reverted.\n";

        return false;
    }
    */
}
