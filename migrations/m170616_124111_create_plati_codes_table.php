<?php

use yii\db\Migration;

/**
 * Handles the creation of table `plati_codes`.
 */
class m170616_124111_create_plati_codes_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('plati_codes', [
            'id'        => $this->primaryKey(),
            'code'      => $this->string(),
            'checks'    => $this->smallInteger(),
            'user_id'   => $this->integer()->defaultValue(null),
            'tm_create' => $this->timestamp()->defaultExpression('NOW()'),
            'tm_used'   => $this->timestamp(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('plati_codes');
    }
}
