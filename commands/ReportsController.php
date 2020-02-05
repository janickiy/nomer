<?php
namespace app\commands;

use app\models\AppleSubscribeEvent;
use yii\console\Controller;

class ReportsController extends Controller {

    public function actionIndex() {
        for($i = 1; $i <= 15; $i++) {
            if($i < 10) $ii = '0'.$i;
            else $ii = $i;
            $f = fopen(\Yii::getAlias('@runtime').'/Subscription_Event_87428184_201801'.$ii.'_V1_1.txt', 'r');
            //$data = fgetcsv($f, 1024, "\t");
            $row = 0;
            while (($data = fgetcsv($f, 1000, "\t")) !== FALSE) {
                if($row == 0) { $row++; continue; }
                $e = new AppleSubscribeEvent();
                $e->event_date = $data[0];
                $e->event = $data[1];
                $e->app_name = $data[2];
                $e->app_id = (int)$data[3];
                $e->subscription_name = $data[4];
                $e->subscription_id = (int)$data[5];
                $e->subscription_group_id = (int)$data[6];
                $e->subscription_duration = $data[7];
                $e->introductory_price_type = $data[8];
                $e->introductory_price_duration = $data[9];
                $e->marketing_opt_in = $data[10];
                $e->marketing_opt_in_duration = $data[11];
                $e->preserved_pricing = $data[12];
                $e->proceeds_reason = $data[13];
                $e->consecutive_paid_periods = $data[14];
                $e->original_start_date = $data[15];
                $e->client = $data[16];
                $e->device = $data[17];
                $e->state = $data[18];
                $e->country = $data[19];
                $e->previous_subscription_name = $data[20];
                $e->previous_subscription_id = (int)$data[21];
                $e->days_before_canceling = (int)$data[22];
                $e->cancellation_reason = $data[23];
                $e->days_canceled = (int)$data[24];
                $e->quantity = (int)$data[25];
                if(!$e->save()) {
                    print_r($e->getErrors()); die();
                }

                $row++;
            }

            fclose($f);
        }
    }
/*
Array
(
[22] => Days Before Canceling
[23] => Cancellation Reason
[24] => Days Canceled
[25] => Quantity
)
*/
}