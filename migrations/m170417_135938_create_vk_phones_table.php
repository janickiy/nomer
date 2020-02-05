<?php

use yii\db\Migration;

/**
 * Handles the creation of table `vk_phones`.
 */
class m170417_135938_create_vk_phones_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('vk_phones', [
            'id' => $this->bigPrimaryKey(),
            'phone' => $this->string()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('vk_phones');
    }
}
