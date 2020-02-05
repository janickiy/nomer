<?php

use yii\db\Migration;

class m170619_131613_add_platiru_to_sites extends Migration
{
    public function up()
    {
        $this->addColumn(\app\models\Site::tableName(), "platiru_id", $this->integer());
    }

    public function down()
    {
        echo "m170619_131613_add_platiru_to_sites cannot be reverted.\n";

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
