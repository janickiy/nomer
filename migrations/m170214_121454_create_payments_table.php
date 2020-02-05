<?php

use yii\db\Migration;

/**
 * Handles the creation of table `payments`.
 */
class m170214_121454_create_payments_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('payments', [
            'id'                => $this->primaryKey(),
            'sum'               => $this->decimal(24, 4),
            'amount'            => $this->decimal(24, 4),
            'user_id'           => $this->integer(),
            'tm'                => $this->timestamp(),
            'operation_id'      => $this->string(),
            'operation_label'   => $this->string()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('payments');
    }
}
