<?php

use yii\db\Migration;

/**
 * Handles the creation of table `vk_raw`.
 */
class m170512_124345_create_vk_raw_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('vk_raw', [
            'id' => $this->bigInteger(),
            'data' => $this->text()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('vk_raw');
    }
}
