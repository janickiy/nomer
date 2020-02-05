<?php

use yii\db\Migration;

/**
 * Handles the creation of table `users`.
 */
class m170210_085747_create_users_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('users', [
            'id'            => $this->primaryKey(),
            'email'         => $this->string(),
            'password'      => $this->string(),
            'nick'          => $this->string(),
            'phone'         => $this->string(),
            'code'          => $this->string(),
            'auth_key'      => $this->string(),
            'balance'       => $this->decimal(24, 4)->defaultValue(0),
            'tm_create'     => $this->dateTime(),
            'tm_update'     => $this->dateTime(),
            'tm_last_auth'  => $this->dateTime()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('users');
    }
}
