<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user_settings`.
 */
class m170412_083035_create_user_settings_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('user_settings', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'param' => $this->string(),
            'value' => $this->string()
        ]);

        $this->createIndex("idx_user_settings_user_id", "user_settings", "user_id");
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropIndex("idx_user_settings_user_id", "user_settings");
        $this->dropTable('user_settings');
    }
}
