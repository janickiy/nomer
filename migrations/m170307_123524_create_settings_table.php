<?php

use yii\db\Migration;

/**
 * Handles the creation of table `settings`.
 */
class m170307_123524_create_settings_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('settings', [
            'id' => $this->primaryKey(),
            'param' => $this->string(),
            'value' => $this->string()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('settings');
    }
}
