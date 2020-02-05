<?php

use yii\db\Migration;

class m170518_072322_add_payment_type extends Migration
{
    public function up()
    {
        $this->addColumn(\app\models\Payment::tableName(), "type_id", $this->smallInteger());
    }

    public function down()
    {
        echo "m170518_072322_add_payment_type cannot be reverted.\n";

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
