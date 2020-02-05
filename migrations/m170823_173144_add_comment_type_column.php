<?php

use yii\db\Migration;

class m170823_173144_add_comment_type_column extends Migration
{
    public function safeUp()
    {
        $this->addColumn(\app\models\TicketComment::tableName(), "type_id", $this->smallInteger(1)->defaultValue(0));
    }

    public function safeDown()
    {
        echo "m170823_173144_add_comment_type_column cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170823_173144_add_comment_type_column cannot be reverted.\n";

        return false;
    }
    */
}
