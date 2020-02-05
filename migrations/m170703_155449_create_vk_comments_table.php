<?php

use yii\db\Migration;

/**
 * Handles the creation of table `vk_comments`.
 */
class m170703_155449_create_vk_comments_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('vk_comments', [
            'id'        => $this->integer(),
            'pid'       => $this->integer()->defaultValue(0),
            'site_id'   => $this->smallInteger(),
            'tm'        => $this->dateTime(),
            'name'      => $this->string(),
            'comment'   => $this->text(),
            'vk_id'     => $this->string(),
            'photo'     => $this->text()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('vk_comments');
    }
}
