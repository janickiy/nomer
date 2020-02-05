<?php

use yii\db\Migration;

/**
 * Handles the creation of table `tickets`.
 */
class m170822_144025_create_tickets_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('tickets', [
            'id'            => $this->primaryKey(),
            'user_id'       => $this->integer(),
            'site_id'       => $this->smallInteger(),
            'ip'            => $this->string(15),
            'subject_id'    => $this->smallInteger(2),
            'text'          => $this->text(),
            'status'        => $this->smallInteger(1),
            'tm_create'     => $this->dateTime(),
            'tm_read'       => $this->dateTime(),
            'tm_close'      => $this->dateTime(),
            'tm_reopen'     => $this->dateTime(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('tickets');
    }
}
