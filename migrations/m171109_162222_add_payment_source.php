<?php

use yii\db\Migration;

class m171109_162222_add_payment_source extends Migration
{
    public function safeUp()
    {
        $this->addColumn(\app\models\Payment::tableName(), "source", $this->string());
    }

    public function safeDown()
    {
        echo "m171109_162222_add_payment_source cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171109_162222_add_payment_source cannot be reverted.\n";

        return false;
    }
    */
}
