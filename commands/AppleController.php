<?php

namespace app\commands;

use app\models\ApplePayment;
use yii\console\Controller;

class AppleController extends Controller
{

    public function actionIndex()
    {
        $d = date("Ymd", strtotime("-2 days"));
        $s = $d."_V1_1";
        $data = `cd /home/nomer.io/reports && java -jar /home/nomer.io/reports/Reporter.jar p=Reporter.properties m=Robot.XML Sales.getReport 87428184, Subscriber, Detailed, Daily, $d, 1_1`;
        $result = `zcat /home/nomer.io/reports/Subscriber_87428184_$s.txt.gz`;
        $rows = explode("\n", $result);
        foreach ($rows as $i => $r) {
            if ($i == 0) continue;
            $items = explode("\t", $r);
            print_r($r);
            if (count($items) < 10) continue;

            $payment = new ApplePayment();
            $payment->tm = $items[0];
            $payment->sum = $items[9];
            $payment->amount = $items[11];
            $payment->refund = $items[20] == "Yes" ? 1 : 0;
            if (!$payment->save()) {
                print_r($payment->getErrors());
                die();
            }
        }
    }
}