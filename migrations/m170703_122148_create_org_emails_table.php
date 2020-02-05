<?php

use yii\db\Migration;

/**
 * Handles the creation of table `org_emails`.
 */
class m170703_122148_create_org_emails_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('org_emails', [
            'id' => $this->primaryKey(),
            'org_id' => $this->integer(),
            'name' => $this->string(),
            'email' => $this->string()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('org_emails');
    }
}
