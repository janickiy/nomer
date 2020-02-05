<?php

use yii\db\Migration;

/**
 * Handles the creation of table `notification_results`.
 */
class m171108_113716_create_notification_results_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('notification_results', [
            'id' => $this->primaryKey(),
            'notify_id' => $this->integer(),
            'user_id' => $this->integer(),
            'status' => $this->smallInteger()->defaultValue(0)
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('notification_results');
    }
}
