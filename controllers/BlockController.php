<?php

namespace app\controllers;

use app\models\forms\BlockForm;
use app\models\Site;
use Yii;
use yii\db\Expression;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;
use app\models\BlockPhone;

class BlockController extends Controller
{
    public function actionIndex()
    {

        $phone = false;

        $model = new BlockForm();
        if($model->load(\Yii::$app->request->post()) && $model->validate()) {
            $phone = preg_replace('/[^0-9]/', '', $model->phone);

            $block = BlockPhone::find()->where(["phone" => $phone, "status" => [1, 2]])->one();
            if(!$block) {
                $code = sprintf("%'.03d", rand(0, 999));

                $site = Site::find()->where(["name" => \Yii::$app->request->getHostName()])->one();

                $block = BlockPhone::find()->where(["phone" => $phone, "site_id" => $site->id])->one();

                if (is_null($block)) {
                    $block          = new BlockPhone();
                    $block->phone   = (string) $phone;
                    $block->ip      = Yii::$app->getRequest()->getUserIP();
                    $block->ua      = Yii::$app->getRequest()->getUserAgent();
                    $block->tm      = new Expression("NOW()");
                    $block->code    = (string) $code;
                    $block->site_id = $site->id;

                    if ($block->save()) {
                        /*
                        $url = Url::to(['https://smsc.ru/sys/send.php',
                            'login'     => 'admeo',
                            'psw'       => 'admeosmsc',
                            'phones'    => $phone,
                            'mes'       => 'Ваш код: ' . $code,
                            'charset'   => 'utf-8',
                            'sender'    => Yii::$app->name
                        ], 'https');
                        */

                        Yii::$app->session->set('lastBlockPhone', $phone);

                        $codeTxt = str_split($code, 1);
                        $codeTxt = join(" ", $codeTxt);


                        $request = curl_init("http://asterisk.apinomer.com:8101/call");
                        curl_setopt_array($request, [
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_POST => true,
                            CURLOPT_POSTFIELDS => Json::encode(["phone" => $phone, "code" => $codeTxt]),
                            CURLOPT_HTTPHEADER => ['Content-type: application/json'],
                        ]);

                        curl_exec($request);

                        //file_get_contents($url);
                        //Yii::$app->session->set('lastBlockPhone', $phone);
                        return $this->redirect(['block/confirm']);
                    }
                } else {
//                    $block = BlockPhone::find()->where(["phone" => $phone, "site_id" => $site->id])->one();
//                    if(!$block) {
                        $block          = new BlockPhone();
                        $block->phone   = (string) $phone;
                        $block->ip      = Yii::$app->getRequest()->getUserIP();
                        $block->ua      = Yii::$app->getRequest()->getUserAgent();
                        $block->tm      = new Expression("NOW()");
                        $block->site_id = $site->id;
                        $block->save();
//                    }
                    Yii::$app->session->set('lastBlockPhone', $phone);
                    return $this->redirect(['block/confirm']);
                }
            }
        }

        return $this->render('index', [
            "model" => $model,
            "phone" => $phone
        ]);
    }

    public function actionSms()
    {
        if(Yii::$app->session->get('smsBlockPhone')) {
            return $this->redirect(["block/confirm"]);
        };

        $phone = Yii::$app->session->get('lastBlockPhone', null);

        $site = Site::find()->where(["name" => \Yii::$app->request->getHostName()])->one();
        $block = BlockPhone::find()->where(["phone" => $phone, "site_id" => $site->id])->one();

        Yii::$app->session->set('smsBlockPhone', true);

        $url = Url::to(['https://smsc.ru/sys/send.php',
            'login'     => 'admeo',
            'psw'       => 'admeosmsc',
            'phones'    => $phone,
            'mes'       => 'Ваш код: ' . $block->code,
            'charset'   => 'utf-8',
            'sender'    => Yii::$app->name
        ], 'https');

        file_get_contents($url);

        return $this->redirect(["block/confirm"]);
    }

    public function actionConfirm()
    {
        $phone = Yii::$app->session->get('lastBlockPhone', null);

        if (is_null($phone)) {
            return $this->redirect(['block/index']);
        }

        if (Yii::$app->request->isPost) {
            $code = Yii::$app->request->post('code');
            $code = preg_replace('/[^0-9]/', '', $code);

            $site = Site::find()->where(["name" => \Yii::$app->request->getHostName()])->one();

            $block = BlockPhone::find()->where(["phone" => $phone, "code" => $code, "site_id" => $site->id])->one();

            if (!is_null($block)) {
                $block->status = 1;
                $block->save();
//                Yii::$app->session->remove('lastBlockPhone');
//                return $this->goHome();
                return $this->redirect(['block/pay']);
            }
        }

        return $this->render('confirm', compact('phone'));
    }

    public function actionRecall() {
        $phone = Yii::$app->session->get('lastBlockPhone', null);
        Yii::$app->session->set('recallBlockPhone', true);

        $block = BlockPhone::find()->where(["phone" => $phone])->one();

        if(!$block) {
            return $this->redirect(['block/confirm']);
        }

        $request = curl_init("http://asterisk.apinomer.com:8101/call");
        curl_setopt_array($request, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => Json::encode(["phone" => $phone, "code" => $block->code]),
            CURLOPT_HTTPHEADER => ['Content-type: application/json'],
        ]);

        curl_exec($request);

        //file_get_contents($url);
        return $this->redirect(['block/confirm']);
    }

    public function actionPay()
    {
        $phone = Yii::$app->session->get('lastBlockPhone', null);

        if (is_null($phone) || !BlockPhone::find()->where(['phone' => $phone, 'status' => BlockPhone::STATUS_CONFIRMED])->one()) {
            return $this->goHome();
        }

        $price = 299;

        return $this->render('pay', compact('phone', 'price'));
    }

    public function actionDeclinePay()
    {
        Yii::$app->session->remove('lastBlockPhone');
        return $this->goHome();
    }

    public function actionPaySuccess()
    {
        Yii::$app->session->remove('lastBlockPhone');
        return $this->render('success');
    }
}
