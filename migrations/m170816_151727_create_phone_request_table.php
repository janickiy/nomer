<?php

use yii\db\Migration;

/**
 * Handles the creation of table `phone_request`.
 */
class m170816_151727_create_phone_request_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('phone_request', [
            'id'        => $this->primaryKey(),
            'user_id'   => $this->integer()->defaultValue(null),
            'tm'        => $this->dateTime()->defaultExpression('NOW()'),
            'ip'        => $this->string(),
            'data'      => $this->text(),
            'wallet'    => $this->string(),
            'contact'   => $this->string(),
            'status'    => $this->smallInteger()->defaultValue(0)
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('phone_request');
    }
}
