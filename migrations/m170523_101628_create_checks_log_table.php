<?php

use yii\db\Migration;

/**
 * Handles the creation of table `checks_log`.
 */
class m170523_101628_create_checks_log_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('checks_log', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'cehcks' => $this->smallInteger(),
            'admin_id' => $this->integer(),
            'tm' => $this->timestamp()->defaultExpression('NOW()')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('checks_log');
    }
}
