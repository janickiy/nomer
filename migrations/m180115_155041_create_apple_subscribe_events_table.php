<?php

use yii\db\Migration;

/**
 * Handles the creation of table `apple_subscribe_events`.
 */
class m180115_155041_create_apple_subscribe_events_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('apple_subscribe_events', [
            'id' => $this->primaryKey(),
            'event_date' => $this->date(),
            'event' => $this->string(),
            'app_name' => $this->string(),
            'app_id' => $this->bigInteger(),
            'subscription_name' => $this->string(),
            'subscription_id' => $this->bigInteger(),
            'subscription_group_id' => $this->bigInteger(),
            'subscription_duration' => $this->string(),
            'introductory_price_type' => $this->string(),
            'introductory_price_duration' => $this->string(),
            'marketing_opt_in' => $this->string(),
            'marketing_opt_in_duration' => $this->string(),
            'preserved_pricing' => $this->string(),
            'proceeds_reason' => $this->string(),
            'consecutive_paid_periods' => $this->smallInteger(),
            'original_start_date' => $this->date(),
            'client' => $this->string(),
            'device' => $this->string(),
            'state' => $this->string(),
            'country' => $this->string(),
            'previous_subscription_name' => $this->string(),
            'previous_subscription_id' => $this->bigInteger(),
            'days_before_canceling' => $this->smallInteger(),
            'cancellation_reason' => $this->string(),
            'days_canceled' => $this->smallInteger(),
            'quantity' => $this->smallInteger(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('apple_subscribe_events');
    }
}
