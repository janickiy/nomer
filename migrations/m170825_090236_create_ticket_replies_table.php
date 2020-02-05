<?php

use yii\db\Migration;

/**
 * Handles the creation of table `ticket_replies`.
 */
class m170825_090236_create_ticket_replies_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('ticket_replies', [
            'id'            => $this->primaryKey(),
            'subject_id'    => $this->smallInteger(),
            'text'          => $this->text()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('ticket_replies');
    }
}
