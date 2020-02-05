<?php

use yii\db\Migration;

class m171108_113652_add_token_to_user extends Migration
{
    public function safeUp()
    {
        $this->addColumn(\app\models\User::tableName(), "token", $this->string());
    }

    public function safeDown()
    {
        echo "m171108_113652_add_token_to_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171108_113652_add_token_to_user cannot be reverted.\n";

        return false;
    }
    */
}
