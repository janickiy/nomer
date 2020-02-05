<?php

use yii\db\Migration;

/**
 * Handles the creation of table `ticket_comments`.
 */
class m170822_144033_create_ticket_comments_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('ticket_comments', [
            'id'        => $this->primaryKey(),
            'ticket_id' => $this->integer(),
            'user_id'   => $this->integer(),
            'text'      => $this->text(),
            'tm_create' => $this->timestamp(),
            'tm_read'   => $this->timestamp()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('ticket_comments');
    }
}
