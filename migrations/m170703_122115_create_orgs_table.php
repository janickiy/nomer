<?php

use yii\db\Migration;

/**
 * Handles the creation of table `orgs`.
 */
class m170703_122115_create_orgs_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('orgs', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'date' => $this->string(),
            'maximum_sum' => $this->decimal(20, 4),
            'inn' => $this->string(),
            'number' => $this->string(),
            'region' => $this->string()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('orgs');
    }
}
