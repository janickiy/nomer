<?php

use yii\db\Migration;

class m170629_145257_add_fields_to_wallet extends Migration
{
    public function up()
    {
        $this->addColumn(\app\models\Wallet::tableName(), "phone", $this->string(11));
        $this->addColumn(\app\models\Wallet::tableName(), "comment", $this->text());
    }

    public function down()
    {
        $this->dropColumn(\app\models\Wallet::tableName(), "phone");
        $this->dropColumn(\app\models\Wallet::tableName(), "comment");
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
