<?php

use yii\db\Migration;

/**
 * Handles the creation of table `apple`.
 */
class m180212_111851_create_apple_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('apple_payments', [
            'id' => $this->primaryKey(),
            'tm' => $this->date(),
            'sum' => $this->decimal(24, 4),
            'amount' => $this->decimal(24, 4),
            'refund' => $this->decimal(24, 4)
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('apple');
    }
}