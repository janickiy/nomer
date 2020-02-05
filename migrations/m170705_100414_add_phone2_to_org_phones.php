<?php

use yii\db\Migration;

class m170705_100414_add_phone2_to_org_phones extends Migration
{
    public function safeUp()
    {
        $this->addColumn('org_phones', 'phone2', $this->string());
    }

    public function safeDown()
    {
        $this->dropColumn('org_phones', 'phone2');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170705_100414_add_phone2_to_org_phones cannot be reverted.\n";

        return false;
    }
    */
}
