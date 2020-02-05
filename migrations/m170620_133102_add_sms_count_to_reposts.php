<?php

use yii\db\Migration;

class m170620_133102_add_sms_count_to_reposts extends Migration
{
    public function up()
    {
        $this->addColumn(\app\models\Repost::tableName(), "sms_count", $this->smallInteger());
    }

    public function down()
    {
        echo "m170620_133102_add_sms_count_to_reposts cannot be reverted.\n";

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
