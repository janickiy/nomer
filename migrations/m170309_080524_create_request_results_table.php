<?php

use yii\db\Migration;

/**
 * Handles the creation of table `request_results`.
 */
class m170309_080524_create_request_results_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('request_results', [
            'id'            => $this->bigPrimaryKey(),
            'request_id'    => $this->bigInteger(),
            'type_id'       => $this->smallInteger(2),
            'index'         => $this->smallInteger(2),
            'data'          => $this->text(),
            'cache_id'      => $this->bigInteger()->defaultValue(null)
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('request_results');
    }
}
