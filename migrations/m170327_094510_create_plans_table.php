<?php

use yii\db\Migration;

/**
 * Handles the creation of table `plans`.
 */
class m170327_094510_create_plans_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('plans', [
            'id' => $this->primaryKey(),
            'cost'  => $this->integer(),
            'count' => $this->integer(),
            'title' => $this->string(),
            'status' => $this->boolean()->defaultValue(true)
        ]);

        $this->insert("plans", [
            "cost"  => 40,
            "count" => 30,
            "title" => "Предоплаченный-30"
        ]);

        $this->insert("plans", [
            "cost"  => 30,
            "count" => 50,
            "title" => "Предоплаченный-50"
        ]);

        $this->insert("plans", [
            "cost"  => 25,
            "count" => 100,
            "title" => "Предоплаченный-100"
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('plans');
    }
}
