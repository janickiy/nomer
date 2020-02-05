<?php

use yii\db\Migration;

class m170710_080032_add_site_id_to_block extends Migration
{
    public function safeUp()
    {
        $this->addColumn(\app\models\BlockPhone::tableName(), "site_id", $this->smallInteger()->defaultValue(0));
    }

    public function safeDown()
    {
        $this->dropColumn(\app\models\BlockPhone::tableName(), "site_id");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170710_080032_add_site_id_to_block cannot be reverted.\n";

        return false;
    }
    */
}
