<?php

use yii\db\Migration;

/**
 * Handles the creation of table `mail_queue`.
 */
class m180713_145036_create_mail_queue_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('mail_queue', [
            'id' => $this->primaryKey(),
            'tm_create' => $this->timestamp()->defaultExpression('NOW()'),
            'tm_send' => $this->timestamp()->defaultValue(null),
            'recipient' => $this->string(),
            'subject' => $this->string(),
            'message' => $this->text(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('mail_queue');
    }
}