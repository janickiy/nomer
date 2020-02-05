<?php

use yii\db\Migration;

class m180106_094343_add_status_to_subs extends Migration
{
    public function safeUp()
    {
        $this->addColumn(\app\models\UserSub::tableName(), "status", $this->smallInteger()->defaultValue(1));
    }

    public function safeDown()
    {
        echo "m180106_094343_add_status_to_subs cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180106_094343_add_status_to_subs cannot be reverted.\n";

        return false;
    }
    */
}
