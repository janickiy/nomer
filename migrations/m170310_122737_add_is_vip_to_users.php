<?php

use app\models\User;
use yii\db\Migration;

class m170310_122737_add_is_vip_to_users extends Migration
{

    public function up()
    {
        $this->addColumn(User::tableName(), "is_vip", $this->boolean()->defaultValue(false));
    }

    public function down()
    {
        $this->dropColumn(User::tableName(), "is_vip");
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
