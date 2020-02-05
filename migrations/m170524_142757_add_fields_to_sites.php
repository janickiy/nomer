<?php

use yii\db\Migration;

class m170524_142757_add_fields_to_sites extends Migration
{
    public function up()
    {
        $this->dropColumn('sites', 'analytics');
        $this->dropColumn('sites', 'metrika');
        $this->addColumn('sites', 'phone', $this->string());
        $this->addColumn('sites', 'yandex_money_account', $this->string());
    }

    public function down()
    {
        $this->dropColumn('sites', 'yandex_money_account');
        $this->dropColumn('sites', 'phone');
        $this->addColumn('sites', 'metrika', $this->text());
        $this->addColumn('sites', 'analytics', $this->text());
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
