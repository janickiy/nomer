<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user_auth_log`.
 */
class m170531_084058_create_user_auth_log_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('user_auth_log', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'site_id' => $this->smallInteger(),
            'ip' => $this->string(),
            'tm' => $this->timestamp()
        ]);

        $this->createIndex("idx_user_auth_log_user_id", "user_auth_log", "user_id");
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropIndex("idx_user_auth_log_user_id", "user_auth_log");

        $this->dropTable('user_auth_log');
    }
}
