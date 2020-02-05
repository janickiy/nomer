<?php

use yii\db\Migration;

/**
 * Handles the creation of table `webmoney_orders`.
 */
class m170522_113829_create_webmoney_orders_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('webmoney_orders', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'tm_create' => $this->timestamp()->defaultExpression('NOW()'),
            'sum' => $this->string(),
            'status' => $this->smallInteger()->defaultValue(0)
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('webmoney_orders');
    }
}
