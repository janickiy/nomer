<?php
namespace app\commands;

use app\components\Curl;
use app\models\BlockPhone;
use app\models\Payment;
use app\models\PhoneRequest;
use app\models\Site;
use app\models\Ticket;
use app\models\User;
use PHPHtmlParser\Dom;
use app\components\Qiwi;
use yii\console\Controller;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class QiwiController extends Controller {

    private $Qiwi = null;

    protected function getProxy()
    {
        $cachedProxy = \Yii::$app->getCache()->get('proxy');

        try {
            $proxies = file_get_contents('http://nalevo.net/qiwiproxy.php');
            $proxies = Json::decode($proxies);
            if (count($proxies) == 0) throw new \Exception();
        } catch (\Exception $e) {
            if (!$cachedProxy) throw new \Exception('No proxy available');
            return $cachedProxy;
        }

        $proxy = $proxies[array_rand($proxies)];
        \Yii::$app->getCache()->set('proxy', $proxy);
        return $proxy;
    }

    public function actionTest() {
        //$proxy = 'socks5://proxy:q2LVelfhoNbo@' . $this->getProxy();

        //$proxy_ip = file_get_contents("https://awmproxy.com/getproxy.php?country=ru");
        //$proxy = 'socks5://'.trim($proxy_ip);
        $proxy = 'socks5://TG:tel.gg@proxy.rip:50000';

        $ruCaptcha = [
            'proxyType' => 'SOCKS5',
            'apiKey' => '0d4004a0d4b7510706ca98dd09f3ec17',
            'googleToken' => '6LfjX_4SAAAAAFfINkDklY_r2Q5BRiEqmLjs4UAC'
        ];

        $sites = Site::find()->where(["!=", "phone", ""])->andWhere(["is not", "phone", null])->orderBy(["id" => SORT_ASC])->all();

        foreach($sites as $s) {
            $this->Qiwi = new Qiwi(null, null, '', $proxy, $ruCaptcha);
            //$this->Qiwi->setCookieFile(\Yii::getAlias('@runtime').'/qiwi_cache0'.$s->id.'.php');
            $this->Qiwi->setCookieFile(\Yii::getAlias('@runtime').'/qiwi'.$s->phone.'.cookie');

            echo $s->phone."\n";

            $p = "Ag6K2oxG2";
            if($s->id == 23) {
                //$p = "Ag6K2oxGG";
            }

            try {
                $this->Qiwi->login($s->phone, $p);
            } catch (\Exception $e) {
                print_r($e);
            }

            try {
                $transactions = $this->Qiwi->transactions(TRUE, date("d.m.Y", (time()-86400*7)), date("d.m.Y", time()+86400));
                $this->process($transactions, $s->id);
            } catch (\Exception $e) {
                echo "error get transactions\n";
            }
        }

    }

    private function process($transactions, $siteID) {

        foreach($transactions as $t) {
            if(ArrayHelper::getValue($t, "incoming") !== true) continue;
            if(ArrayHelper::getValue($t, "status") != "SUCCESS") continue;
            $payment = Payment::find()->where(["operation_id" => ArrayHelper::getValue($t, "id"), "type_id" => [Payment::TYPE_QIWI_TERMINAL, Payment::TYPE_QIWI]])->one();
            if(!$payment) {
                $comment = ArrayHelper::getValue($t, 'comment');

                $sum = ArrayHelper::getValue($t, 'cash');
                $operation_label = (string) ArrayHelper::getValue($t, 'opNumber');

                $blockPayment = strlen($comment) == 11;

                if ($blockPayment) {
                    $blockedPhone = BlockPhone::find()->where(['phone' => $comment, 'status' => BlockPhone::STATUS_CONFIRMED])->one();

                    if (!$blockedPhone) continue;

                    if (preg_match('/\+(\d{11})/', $operation_label) && $sum < 299) {
                        $sum = $sum + $sum * 0.07;
                    }

                    if ($sum >= 299) {
                        $blockedPhone->status = BlockPhone::STATUS_PAID;
                        $blockedPhone->save();
                    }

                    $user_id = null;
                } else {
                    $user_id = (int) $comment;
                }

                $balance = true;

                $payment = new Payment();
                $payment->amount = $sum;
                $payment->sum = $sum;
                $payment->tm = date('Y-m-d H:i:s', strtotime('+3 hour'));
                $payment->operation_id = (string) ArrayHelper::getValue($t, 'id');
                $payment->operation_label = $operation_label;
                $payment->user_id = $user_id;
                $payment->site_id = $siteID;
                if (preg_match('/\+(\d{11})/', $operation_label)) {
                    $payment->type_id = Payment::TYPE_QIWI;
                } else {
                    $payment->type_id = Payment::TYPE_QIWI_TERMINAL;
                    if ($sum < \Yii::$app->params['cost']) {
                        $sum = $sum + $sum * 0.07;
                        $balance = false;
                    }
                }

                $payment->save();

                if($payment->user_id) {
                    if($payment->sum == 1000) {
                        $findPhone = PhoneRequest::find()->where(["user_id" => $payment->user_id])->orderBy(["id" => SORT_DESC])->one();
                        $ticket = new Ticket();
                        $ticket->detachBehavior("user_id");
                        $ticket->user_id = $payment->user_id;
                        $ticket->site_id = $siteID;
                        $ticket->subject_id = 1;
                        $ticket->text = $findPhone->data;
                        $ticket->subject = "Запрос на поиск номера телефона";
                        $ticket->status = 0;
                        $ticket->is_payed = true;
                        $ticket->tm_create = new Expression('NOW()');
                        $ticket->save(false);
                    } else {
                        $user = User::find()->where(['id' => $payment->user_id])->one();
                        if(!$blockPayment) {
                            $user->addBalance($sum, $sum, $balance, $payment->site_id);
                        } else {
                            $user->addBalance(0, $sum, true, $payment->site_id);
                        }
                    }
                }



                $this->Qiwi->paymentQiwi('+79269516206', $payment->sum, "RUB", "RUB");
            }
        }
    }
}
