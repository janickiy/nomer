<?php

use yii\db\Migration;

class m170301_071147_add_tm_field_to_cache extends Migration
{
    public function up()
    {
        $this->addColumn("cache", "tm", $this->timestamp()->defaultExpression('NOW()'));
    }

    public function down()
    {
        $this->dropColumn("cache", "tm");
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
