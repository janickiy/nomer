<?php

use yii\db\Migration;

/**
 * Handles the creation of table `tests`.
 */
class m170405_122703_create_tests_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('tests', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'tm' => $this->dateTime(),
            'ip' => $this->string()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('tests');
    }
}
