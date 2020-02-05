<?php

use yii\db\Migration;

/**
 * Handles the creation of table `notifications`.
 */
class m171108_113709_create_notifications_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('notifications', [
            'id'        => $this->primaryKey(),
            'message'   => $this->string(),
            'payload'   => $this->text(),
            'tm_create' => $this->dateTime(),
            'tm_send'   => $this->dateTime()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('notifications');
    }
}
