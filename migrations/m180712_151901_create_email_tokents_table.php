<?php

use yii\db\Migration;

/**
 * Handles the creation of table `email_tokents`.
 */
class m180712_151901_create_email_tokents_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('email_tokents', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'uuid' => $this->string(),
            'tm_create' => $this->timestamp()->defaultExpression('NOW()'),
            'tm_send'   => $this->timestamp()->defaultValue(null)->comment('время отправки'),
            'tm_read'   => $this->timestamp()->defaultValue(null)->comment('время прочтения'),
            'tm_click'   => $this->timestamp()->defaultValue(null)->comment('момент перехода по ссылки из письма'),
            'status' => $this->smallInteger()->defaultValue(0)->comment('письмо в очереди на отправку, 1 - письмо отправлено, 2 - письмо прочитано, 3 - по ссылке был сделан переход. 4 - ошибка отправки'),
            'descr' => $this->text()->defaultValue(null)->comment('причина ошибка отправки')
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('email_tokents');
    }
}
