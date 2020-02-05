<?php

use yii\db\Migration;

class m170309_130045_add_refresh_to_requests extends Migration
{
    public function up()
    {
        $this->addColumn(\app\models\SearchRequest::tableName(), "refresh", $this->boolean()->defaultValue(false));
    }

    public function down()
    {
        $this->dropColumn(\app\models\SearchRequest::tableName(), "refresh");
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
