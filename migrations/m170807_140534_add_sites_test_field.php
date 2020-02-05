<?php

use yii\db\Migration;

class m170807_140534_add_sites_test_field extends Migration
{
    public function safeUp()
    {
        $this->addColumn(\app\models\Site::tableName(), "type_id", $this->smallInteger()->defaultValue(1));
    }

    public function safeDown()
    {
        $this->dropColumn(\app\models\Site::tableName(), "type_id");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170807_140534_add_sites_test_field cannot be reverted.\n";

        return false;
    }
    */
}
