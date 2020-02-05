<?php

use yii\db\Migration;

class m170626_133943_add_comment_field_to_user extends Migration
{
    public function up()
    {
        $this->addColumn('users', 'comment', $this->string(255));
    }

    public function down()
    {
        $this->dropColumn('users', 'comment');
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
