<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "apple_subscribe_events".
 *
 * @property integer $id
 * @property string $event_date
 * @property string $event
 * @property string $app_name
 * @property integer $app_id
 * @property string $subscription_name
 * @property integer $subscription_id
 * @property integer $subscription_group_id
 * @property string $subscription_duration
 * @property string $introductory_price_type
 * @property string $introductory_price_duration
 * @property string $marketing_opt_in
 * @property string $marketing_opt_in_duration
 * @property string $preserved_pricing
 * @property string $proceeds_reason
 * @property integer $consecutive_paid_periods
 * @property string $original_start_date
 * @property string $client
 * @property string $device
 * @property string $state
 * @property string $country
 * @property string $previous_subscription_name
 * @property integer $previous_subscription_id
 * @property integer $days_before_canceling
 * @property string $cancellation_reason
 * @property integer $days_canceled
 * @property integer $quantity
 */
class AppleSubscribeEvent extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'apple_subscribe_events';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['event_date', 'original_start_date'], 'safe'],
            [['app_id', 'subscription_id', 'subscription_group_id', 'consecutive_paid_periods', 'previous_subscription_id', 'days_before_canceling', 'days_canceled', 'quantity'], 'integer'],
            [['event', 'app_name', 'subscription_name', 'subscription_duration', 'introductory_price_type', 'introductory_price_duration', 'marketing_opt_in', 'marketing_opt_in_duration', 'preserved_pricing', 'proceeds_reason', 'client', 'device', 'state', 'country', 'previous_subscription_name', 'cancellation_reason'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event_date' => 'Event Date',
            'event' => 'Event',
            'app_name' => 'App Name',
            'app_id' => 'App ID',
            'subscription_name' => 'Subscription Name',
            'subscription_id' => 'Subscription ID',
            'subscription_group_id' => 'Subscription Group ID',
            'subscription_duration' => 'Subscription Duration',
            'introductory_price_type' => 'Introductory Price Type',
            'introductory_price_duration' => 'Introductory Price Duration',
            'marketing_opt_in' => 'Marketing Opt In',
            'marketing_opt_in_duration' => 'Marketing Opt In Duration',
            'preserved_pricing' => 'Preserved Pricing',
            'proceeds_reason' => 'Proceeds Reason',
            'consecutive_paid_periods' => 'Consecutive Paid Periods',
            'original_start_date' => 'Original Start Date',
            'client' => 'Client',
            'device' => 'Device',
            'state' => 'State',
            'country' => 'Country',
            'previous_subscription_name' => 'Previous Subscription Name',
            'previous_subscription_id' => 'Previous Subscription ID',
            'days_before_canceling' => 'Days Before Canceling',
            'cancellation_reason' => 'Cancellation Reason',
            'days_canceled' => 'Days Canceled',
            'quantity' => 'Quantity',
        ];
    }
}
