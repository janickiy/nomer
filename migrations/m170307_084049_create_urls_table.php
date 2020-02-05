<?php

use yii\db\Migration;

/**
 * Handles the creation of table `urls`.
 */
class m170307_084049_create_urls_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('urls', [
            'id'    => $this->primaryKey(),
            'url'   => $this->string(),
            'type'  => $this->smallInteger(2)
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('urls');
    }
}
