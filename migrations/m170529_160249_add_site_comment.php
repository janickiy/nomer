<?php

use yii\db\Migration;

class m170529_160249_add_site_comment extends Migration
{
    public function up()
    {
        $this->addColumn(\app\models\Site::tableName(), "comment", $this->string());
    }

    public function down()
    {
        $this->dropColumn(\app\models\Site::tableName(), "comment");
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
