<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user_fingerprints`.
 */
class m170323_114223_create_user_fingerprints_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('user_fingerprints', [
            'id'        => $this->primaryKey(),
            'user_id'   => $this->bigInteger(),
            'ip'        => $this->string(),
            'hash'      => $this->string(),
            'tm'        => $this->timestamp()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('user_fingerprints');
    }
}
