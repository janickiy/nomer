<?php

use yii\db\Migration;

/**
 * Handles the creation of table `telegrams`.
 */
class m170605_102413_create_telegrams_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('telegrams', [
            'id' => $this->primaryKey(),
            'host' => $this->string(),
            'port' => $this->smallInteger(),
            'status' => $this->smallInteger()->defaultValue(1),
            'tm_last' => $this->timestamp()
        ]);

        $instances = [
            '127.0.0.1:1236',
            '5.104.111.8:1236',
            '46.4.69.117:1236',
            '46.4.202.77:1236',
            '46.4.202.75:1236',
            '46.4.202.78:1236',
        ];

        foreach ($instances as $instance) {
            list($host, $port) = explode(':', $instance);

            $this->insert('telegrams', [
                'host' => $host,
                'port' => $port
            ]);
        }
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('telegrams');
    }
}
