<?php

use yii\db\Migration;

/**
 * Handles the creation of table `org_phones`.
 */
class m170703_122126_create_org_phones_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('org_phones', [
            'id' => $this->primaryKey(),
            'org_id' => $this->integer(),
            'name' => $this->string(),
            'phone' => $this->string()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('org_phones');
    }
}
