<?php

use yii\db\Migration;

/**
 * Handles the creation of table `block`.
 */
class m170301_110655_create_block_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('block', [
            'id'        => $this->primaryKey(),
            'phone'     => $this->string(),
            'ip'        => $this->string(),
            'ua'        => $this->string(),
            'tm'        => $this->timestamp(),
            'code'      => $this->string(4),
            'status'    => $this->smallInteger(1)->defaultValue(0)
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('block');
    }
}
