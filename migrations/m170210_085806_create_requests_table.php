<?php

use yii\db\Migration;

/**
 * Handles the creation of table `requests`.
 */
class m170210_085806_create_requests_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('requests', [
            'id'        => $this->primaryKey(),
            'user_id'   => $this->integer(),
            'phone'     => $this->string(),
            'ip'        => $this->string(),
            'ua'        => $this->string(),
            'result'    => $this->text(),
            'tm'        => $this->timestamp()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('requests');
    }
}
