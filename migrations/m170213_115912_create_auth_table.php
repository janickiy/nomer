<?php

use yii\db\Migration;

/**
 * Handles the creation of table `auth`.
 */
class m170213_115912_create_auth_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('auth', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'source' => $this->string(),
            'source_id' => $this->string()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('auth');
    }
}
