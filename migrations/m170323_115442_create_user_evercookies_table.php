<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user_evercookies`.
 */
class m170323_115442_create_user_evercookies_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('user_evercookies', [
            'id'        => $this->primaryKey(),
            'user_id'   => $this->bigInteger(),
            'ip'        => $this->string(),
            'data'      => $this->string(),
            'tm'        => $this->timestamp()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('user_evercookies');
    }
}
