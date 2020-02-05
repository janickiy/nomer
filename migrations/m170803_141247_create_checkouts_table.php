<?php

use yii\db\Migration;

/**
 * Handles the creation of table `checkouts`.
 */
class m170803_141247_create_checkouts_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('checkouts', [
            'id'        => $this->primaryKey(),
            'user_id'   => $this->integer(),
            'wallet'    => $this->string(),
            'sum'       => $this->decimal(24, 4),
            'tm_create' => $this->timestamp(),
            'tm_done'   => $this->timestamp(),
            'status'    => $this->smallInteger(1)->defaultValue(0)
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('checkouts');
    }
}
