<?php

use yii\db\Migration;

/**
 * Handles the creation of table `wallets`.
 */
class m170530_140903_create_wallets_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('wallets', [
            'id' => $this->primaryKey(),
            'type_id' => $this->smallInteger(),
            'wallet_id' => $this->string(),
            'password' => $this->string(),
            'balance' => $this->double(2),
            'tm_last_balance' => $this->timestamp(),
            'tm_last_transaction' => $this->timestamp()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('wallets');
    }
}
