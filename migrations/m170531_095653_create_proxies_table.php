<?php

use yii\db\Migration;

/**
 * Handles the creation of table `proxies`.
 */
class m170531_095653_create_proxies_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('proxies', [
            'id' => $this->primaryKey(),
            'host' => $this->string(),
            'port' => $this->smallInteger(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('proxies');
    }
}
