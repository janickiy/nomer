<?php

use yii\db\Migration;

/**
 * Handles the creation of table `subs`.
 */
class m170915_134028_create_subs_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('subs', [
            'id'                        => $this->primaryKey(),
            'user_id'                   => $this->integer(),
            'transaction_id'            => $this->bigInteger(),
            'original_transaction_id'   => $this->bigInteger(),
            'tm_purchase'               => $this->dateTime(),
            'tm_expires'                => $this->dateTime()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('subs');
    }
}
