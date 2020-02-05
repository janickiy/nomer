<?php

use yii\db\Migration;

class m170825_143708_add_url_to_tickets extends Migration
{
    public function safeUp()
    {
        $this->addColumn(\app\models\Ticket::tableName(), "url", $this->string());
    }

    public function safeDown()
    {
        echo "m170825_143708_add_url_to_tickets cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170825_143708_add_url_to_tickets cannot be reverted.\n";

        return false;
    }
    */
}
