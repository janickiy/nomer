<?php

use yii\db\Migration;

/**
 * Handles the creation of table `links`.
 */
class m170619_135243_create_links_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('links', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'code' => $this->string()->unique(),
            'tm' => $this->timestamp()->defaultExpression('NOW()')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('links');
    }
}
