<?php

use yii\db\Migration;

/**
 * Handles the creation of table `files`.
 */
class m170313_101638_create_files_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('files', [
            'id' => $this->primaryKey(),
            'uuid' => $this->string(),
            'type' => $this->string(16)
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('files');
    }
}
