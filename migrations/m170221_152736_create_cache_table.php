<?php

use yii\db\Migration;

/**
 * Handles the creation of table `cache`.
 */
class m170221_152736_create_cache_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('cache', [
            'id'        => $this->primaryKey(),
            'phone'     => $this->string(),
            'data'      => $this->text(),
            'type_id'   => $this->smallInteger(2)
        ]);

        $this->createIndex('idx_cache_phone', 'cache', 'phone');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropIndex('idx_cache_phone', 'cache');

        $this->dropTable('cache');
    }
}
