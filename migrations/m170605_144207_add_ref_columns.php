<?php

use yii\db\Migration;
use \app\models\User;

class m170605_144207_add_ref_columns extends Migration
{
    public function up()
    {
        $this->addColumn(User::tableName(), "ref_id", $this->integer());
        $this->addColumn(User::tableName(), "ref_checks", $this->decimal(24, 4));
    }

    public function down()
    {
        $this->dropColumn(User::tableName(), "ref_id");
        $this->dropColumn(User::tableName(), "ref_checks");
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
