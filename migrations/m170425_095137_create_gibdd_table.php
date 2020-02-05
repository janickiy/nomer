<?php

use yii\db\Migration;

/**
 * Handles the creation of table `gibdd`.
 */
class m170425_095137_create_gibdd_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('gibdd', [
            'tm' => $this->string(10),
            'number' => $this->string(10),
            'model' => $this->string(64),
            'year' => $this->string(4),
            'lastname' => $this->string(32),
            'firstname' => $this->string(32),
            'middlename' => $this->string(32),
            'phone' => $this->string(11),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('gibdd');
    }
}
