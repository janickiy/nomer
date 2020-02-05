<?php

use yii\db\Migration;

/**
 * Handles the creation of table `calls`.
 */
class m170704_151012_create_calls_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('calls', [
            'id' => $this->primaryKey(),
            'tm' => $this->timestamp(),
            'cuid' => $this->string(),
            'duration' => $this->integer(),
            'status' => $this->string(),
            'phone' => $this->bigInteger()
        ]);

    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('calls');
    }
}
