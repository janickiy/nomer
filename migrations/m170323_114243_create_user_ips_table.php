<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user_ips`.
 */
class m170323_114243_create_user_ips_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('user_ips', [
            'id'        => $this->primaryKey(),
            'user_id'   => $this->bigInteger(),
            'ip'        => $this->string(),
            'tm'        => $this->timestamp()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('user_ips');
    }
}
