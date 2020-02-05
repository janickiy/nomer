<?php

use yii\db\Migration;

/**
 * Handles the creation of table `free`.
 */
class m170714_150526_create_free_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('free', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'checks' => $this->integer(),
            'uuid' => $this->string(),
            'type_id' => $this->smallInteger(),
            'tm' => $this->timestamp()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('free');
    }
}
