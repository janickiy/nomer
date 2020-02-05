<?php

namespace app\modules\api\controllers;

use app\models\Payment;
use app\models\User;
use app\models\UserSub;
use Exception;
use Yii;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use ReceiptValidator\iTunes\Validator as iTunesValidator;
use ReceiptValidator\GooglePlay\Validator as PlayValidator;

class BuyController extends Controller
{

    public function actionStatus()
    {
        /*
        $uuid = Yii::$app->getRequest()->getHeaders()->get('uuid', false);
        if (!$uuid) {
            throw new BadRequestHttpException();
        }
        $user = User::find()->select(["id", "balance", "checks"])->where(["uuid" => $uuid])->one();
        if (!$user) {
            $user = new User();
            $user->email = null;
            $user->uuid = $uuid;
            $user->save();

            $user = User::find()->select(["id", "balance", "checks"])->where(["uuid" => $uuid])->one();
        }
        */

        $f = fopen(\Yii::getAlias('@runtime') . '/apple.log', 'a+');

        $data = Json::decode(\Yii::$app->request->getRawBody());
        //$data = Yii::$app->request->getBodyParams();
        //$receiptBase64Data = ArrayHelper::getValue($data, "receipt");//'ewoJInNpZ25hdHVyZSIgPSAiQXBNVUJDODZBbHpOaWtWNVl0clpBTWlKUWJLOEVkZVhrNjNrV0JBWHpsQzhkWEd1anE0N1puSVlLb0ZFMW9OL0ZTOGNYbEZmcDlZWHQ5aU1CZEwyNTBsUlJtaU5HYnloaXRyeVlWQVFvcmkzMlc5YVIwVDhML2FZVkJkZlcrT3kvUXlQWkVtb05LeGhudDJXTlNVRG9VaFo4Wis0cFA3MHBlNWtVUWxiZElWaEFBQURWekNDQTFNd2dnSTdvQU1DQVFJQ0NHVVVrVTNaV0FTMU1BMEdDU3FHU0liM0RRRUJCUVVBTUg4eEN6QUpCZ05WQkFZVEFsVlRNUk13RVFZRFZRUUtEQXBCY0hCc1pTQkpibU11TVNZd0pBWURWUVFMREIxQmNIQnNaU0JEWlhKMGFXWnBZMkYwYVc5dUlFRjFkR2h2Y21sMGVURXpNREVHQTFVRUF3d3FRWEJ3YkdVZ2FWUjFibVZ6SUZOMGIzSmxJRU5sY25ScFptbGpZWFJwYjI0Z1FYVjBhRzl5YVhSNU1CNFhEVEE1TURZeE5USXlNRFUxTmxvWERURTBNRFl4TkRJeU1EVTFObG93WkRFak1DRUdBMVVFQXd3YVVIVnlZMmhoYzJWU1pXTmxhWEIwUTJWeWRHbG1hV05oZEdVeEd6QVpCZ05WQkFzTUVrRndjR3hsSUdsVWRXNWxjeUJUZEc5eVpURVRNQkVHQTFVRUNnd0tRWEJ3YkdVZ1NXNWpMakVMTUFrR0ExVUVCaE1DVlZNd2daOHdEUVlKS29aSWh2Y05BUUVCQlFBRGdZMEFNSUdKQW9HQkFNclJqRjJjdDRJclNkaVRDaGFJMGc4cHd2L2NtSHM4cC9Sd1YvcnQvOTFYS1ZoTmw0WElCaW1LalFRTmZnSHNEczZ5anUrK0RyS0pFN3VLc3BoTWRkS1lmRkU1ckdYc0FkQkVqQndSSXhleFRldngzSExFRkdBdDFtb0t4NTA5ZGh4dGlJZERnSnYyWWFWczQ5QjB1SnZOZHk2U01xTk5MSHNETHpEUzlvWkhBZ01CQUFHamNqQndNQXdHQTFVZEV3RUIvd1FDTUFBd0h3WURWUjBqQkJnd0ZvQVVOaDNvNHAyQzBnRVl0VEpyRHRkREM1RllRem93RGdZRFZSMFBBUUgvQkFRREFnZUFNQjBHQTFVZERnUVdCQlNwZzRQeUdVakZQaEpYQ0JUTXphTittVjhrOVRBUUJnb3Foa2lHOTJOa0JnVUJCQUlGQURBTkJna3Foa2lHOXcwQkFRVUZBQU9DQVFFQUVhU2JQanRtTjRDL0lCM1FFcEszMlJ4YWNDRFhkVlhBZVZSZVM1RmFaeGMrdDg4cFFQOTNCaUF4dmRXLzNlVFNNR1k1RmJlQVlMM2V0cVA1Z204d3JGb2pYMGlreVZSU3RRKy9BUTBLRWp0cUIwN2tMczlRVWU4Y3pSOFVHZmRNMUV1bVYvVWd2RGQ0TndOWXhMUU1nNFdUUWZna1FRVnk4R1had1ZIZ2JFL1VDNlk3MDUzcEdYQms1MU5QTTN3b3hoZDNnU1JMdlhqK2xvSHNTdGNURXFlOXBCRHBtRzUrc2s0dHcrR0szR01lRU41LytlMVFUOW5wL0tsMW5qK2FCdzdDMHhzeTBiRm5hQWQxY1NTNnhkb3J5L0NVdk02Z3RLc21uT09kcVRlc2JwMGJzOHNuNldxczBDOWRnY3hSSHVPTVoydG04bnBMVW03YXJnT1N6UT09IjsKCSJwdXJjaGFzZS1pbmZvIiA9ICJld29KSW05eWFXZHBibUZzTFhCMWNtTm9ZWE5sTFdSaGRHVXRjSE4wSWlBOUlDSXlNREV5TFRBMExUTXdJREE0T2pBMU9qVTFJRUZ0WlhKcFkyRXZURzl6WDBGdVoyVnNaWE1pT3dvSkltOXlhV2RwYm1Gc0xYUnlZVzV6WVdOMGFXOXVMV2xrSWlBOUlDSXhNREF3TURBd01EUTJNVGM0T0RFM0lqc0tDU0ppZG5KeklpQTlJQ0l5TURFeU1EUXlOeUk3Q2draWRISmhibk5oWTNScGIyNHRhV1FpSUQwZ0lqRXdNREF3TURBd05EWXhOemc0TVRjaU93b0pJbkYxWVc1MGFYUjVJaUE5SUNJeElqc0tDU0p2Y21sbmFXNWhiQzF3ZFhKamFHRnpaUzFrWVhSbExXMXpJaUE5SUNJeE16TTFOems0TXpVMU9EWTRJanNLQ1NKd2NtOWtkV04wTFdsa0lpQTlJQ0pqYjIwdWJXbHVaRzF2WW1Gd2NDNWtiM2R1Ykc5aFpDSTdDZ2tpYVhSbGJTMXBaQ0lnUFNBaU5USXhNVEk1T0RFeUlqc0tDU0ppYVdRaUlEMGdJbU52YlM1dGFXNWtiVzlpWVhCd0xrMXBibVJOYjJJaU93b0pJbkIxY21Ob1lYTmxMV1JoZEdVdGJYTWlJRDBnSWpFek16VTNPVGd6TlRVNE5qZ2lPd29KSW5CMWNtTm9ZWE5sTFdSaGRHVWlJRDBnSWpJd01USXRNRFF0TXpBZ01UVTZNRFU2TlRVZ1JYUmpMMGROVkNJN0Nna2ljSFZ5WTJoaGMyVXRaR0YwWlMxd2MzUWlJRDBnSWpJd01USXRNRFF0TXpBZ01EZzZNRFU2TlRVZ1FXMWxjbWxqWVM5TWIzTmZRVzVuWld4bGN5STdDZ2tpYjNKcFoybHVZV3d0Y0hWeVkyaGhjMlV0WkdGMFpTSWdQU0FpTWpBeE1pMHdOQzB6TUNBeE5Ub3dOVG8xTlNCRmRHTXZSMDFVSWpzS2ZRPT0iOwoJImVudmlyb25tZW50IiA9ICJTYW5kYm94IjsKCSJwb2QiID0gIjEwMCI7Cgkic2lnbmluZy1zdGF0dXMiID0gIjAiOwp9';
        $type = ArrayHelper::getValue($data, "notification_type");
        $env = ArrayHelper::getValue($data, "environment");
        fwrite($f, print_r($data, true) . "\n");

        if(!in_array($type, ["INTERACTIVE_RENEWAL", "RENEWAL"])) return [];

        $p = ArrayHelper::getValue($data, "latest_receipt_info");
        $uuid = ArrayHelper::getValue($p, "unique_vendor_identifier");
        fwrite($f, print_r($p, true) . "\n");
        $originalTransactionID = (string)ArrayHelper::getValue($p, "original_transaction_id");
        $sub = UserSub::find()->where(["original_transaction_id" => $originalTransactionID])->one();
        $user = null;
        if($sub) {
            $user = User::find()->where(["id" => $sub->user_id])->one();
        }
        $transactionID = (string)ArrayHelper::getValue($p, "transaction_id");
        $productID = (string)ArrayHelper::getValue($p, "product_id");
        $payment = Payment::find()->where(["operation_id" => $transactionID])->one();
        if (!$payment) {
            $payment = new Payment();
            $payment->user_id = ArrayHelper::getValue($user, "id", null);
            $payment->tm = new Expression('NOW()');
            if ($env == "PROD") {
                $payment->type_id = Payment::TYPE_APPLE;
            } else {
                $payment->type_id = Payment::TYPE_TESTAPPLE;
            }
            $payment->operation_label = $productID;
            $payment->operation_id = $transactionID;
            $sum = 0;
            $checks = 0;
            if (preg_match("/com\.wcaller\.Wcaller\.search(\d+)/", $productID, $m)) {
                switch ($m[1]) {
                    case 1:
                        $sum = 149;
                        break;
                    case 10:
                        $sum = 749;
                        break;
                    case 100:
                        $sum = 3490;
                        break;
                    case 300:
                        $sum = 8990;
                        break;
                    case 30:
                        $sum = 1390;
                        break;
                }
                $checks = $m[1];
            }
            $isSubscribe = false;
            if (preg_match("/com\.wcaller\.Wcaller\.sub\.month\.(\d+)/", $productID, $m)) {
                switch ($m[1]) {
                    case 0:
                        $sum = 2750;
                        break;
                    case 10:
                        $sum = 199;
                        break;
                    case 15:
                        $sum = 299;
                        break;
                    case 50:
                        $sum = 499;
                        break;
                    case 999:
                        $sum = 1690;
                        break;
                }
                $checks = $m[1];
                $isSubscribe = true;
            }
            if (preg_match("/com\.wcaller\.Wcaller\.sub\.6month\.(\d+)/", $productID, $m)) {
                switch ($m[1]) {
                    case 0:
                        $sum = 6190;
                        break;
                }
                $checks = $m[1];
                $isSubscribe = true;
            }
            if (preg_match("/com\.wcaller\.Wcaller\.sub\.week\.(\d+)/", $productID, $m)) {
                switch ($m[1]) {
                    case 0:
                        $sum = 499;
                        break;
                    case 999:
                        $sum = 249;
                        break;
                }
                $checks = $m[1];
                $isSubscribe = true;
            }
            if($checks == 0 || $checks == 999) $checks = -1;

            $payment->sum = $sum;
            $payment->amount = $sum * 0.59;
            if ($payment->save()) {
                if ($isSubscribe) {
                    $sub = new UserSub();
                    $sub->user_id = $user->id;
                    $sub->transaction_id = ArrayHelper::getValue($p, "transaction_id");
                    $sub->original_transaction_id = ArrayHelper::getValue($p, "original_transaction_id");
                    $sub->tm_purchase = \Yii::$app->formatter->asDatetime(ArrayHelper::getValue($p, "purchase_date"), "yyyy-MM-dd HH:mm:ss");
                    $sub->tm_expires = \Yii::$app->formatter->asDatetime(ArrayHelper::getValue($p, "expires_date")/1000, "yyyy-MM-dd HH:mm:ss");
                    $sub->status = ArrayHelper::getValue($p, "is_trial_period")?0:1;
                    $sub->save();
                }
                $user->checks = $checks;
                $user->save();
            }
        }
        fclose($f);

        return "OK";
    }

    public function actionIndex()
    {
        $userId = \Yii::$app->getRequest()->post("id", false);
        $uuid = Yii::$app->getRequest()->getHeaders()->get('uuid', false);
        if (!$uuid && !$userId) {
            throw new BadRequestHttpException();
        }
        if ($userId) {
            $user = User::find()->select(["id", "balance", "checks"])->where(["id" => $userId])->one();
        } else {
            $user = User::find()->select(["id", "balance", "checks"])->where(["uuid" => $uuid])->one();
        }
        if (!$user) {
            $user = new User();
            $user->email = null;
            $user->uuid = $uuid;
            $user->save();

            $user = User::find()->select(["id", "balance", "checks"])->where(["uuid" => $uuid])->one();
        }

        $isAndroid = Yii::$app->getRequest()->getHeaders()->get('isandroid', false);
        if ($isAndroid) {
            $client = new \Google_Client();
            $client->setApplicationName('nomergg');
            $client->setAuthConfig(\Yii::getAlias('@runtime') . '/nomergg-2842ed9066f5.json');
            $client->setScopes([\Google_Service_AndroidPublisher::ANDROIDPUBLISHER]);

            $validator = new PlayValidator(new \Google_Service_AndroidPublisher($client));

            $packageName = \Yii::$app->request->post("packageName");
            $productId = \Yii::$app->request->post("productId");
            $orderId = \Yii::$app->request->post("orderId");
            $purchaseToken = \Yii::$app->request->post("purchaseToken");

            try {
                $response = $validator->setPackageName($packageName)
                    ->setProductId($productId)
                    ->setPurchaseToken($purchaseToken)
                    ->validatePurchase();
                if (preg_match("/com\.nomergg\.app\.search(\d+)/", $productId, $m)) {
                    $user->checks += $m[1];
                    if ($user->save()) {
                        $payment = new Payment();
                        $payment->user_id = $user->id;
                        $payment->tm = new Expression('NOW()');
                        $payment->type_id = Payment::TYPE_ANDROID;
                        $payment->operation_id = (string)$orderId;
                        $payment->operation_label = $productId;
                        $sum = 0;
                        switch ($m[1]) {
                            case 1:
                                $sum = 98;
                                break;
                            case 10:
                                $sum = 880;
                                break;
                            case 100:
                                $sum = 6800;
                                break;
                            case 30:
                                $sum = 2340;
                                break;
                            case 20:
                                $sum = 1660;
                                break;
                        }
                        $payment->sum = $sum;
                        $payment->amount = $sum * 0.7;
                        $payment->save();
                    }
                }
            } catch (Exception $e) {
                var_dump($e->getMessage());
                // example message: Error calling GET ....: (404) Product not found for this application.
            }
        } else {
            $f = fopen(\Yii::getAlias('@runtime') . '/ios.log', 'a+');
            fwrite($f, $uuid . "\n");
            $validator = new iTunesValidator(iTunesValidator::ENDPOINT_PRODUCTION);


            $data = Json::decode(\Yii::$app->request->getRawBody());
            //$data = Yii::$app->request->getBodyParams();
            $source = ArrayHelper::getValue($data, "source", "");
            $receiptBase64Data = ArrayHelper::getValue($data, "receipt");//'ewoJInNpZ25hdHVyZSIgPSAiQXBNVUJDODZBbHpOaWtWNVl0clpBTWlKUWJLOEVkZVhrNjNrV0JBWHpsQzhkWEd1anE0N1puSVlLb0ZFMW9OL0ZTOGNYbEZmcDlZWHQ5aU1CZEwyNTBsUlJtaU5HYnloaXRyeVlWQVFvcmkzMlc5YVIwVDhML2FZVkJkZlcrT3kvUXlQWkVtb05LeGhudDJXTlNVRG9VaFo4Wis0cFA3MHBlNWtVUWxiZElWaEFBQURWekNDQTFNd2dnSTdvQU1DQVFJQ0NHVVVrVTNaV0FTMU1BMEdDU3FHU0liM0RRRUJCUVVBTUg4eEN6QUpCZ05WQkFZVEFsVlRNUk13RVFZRFZRUUtEQXBCY0hCc1pTQkpibU11TVNZd0pBWURWUVFMREIxQmNIQnNaU0JEWlhKMGFXWnBZMkYwYVc5dUlFRjFkR2h2Y21sMGVURXpNREVHQTFVRUF3d3FRWEJ3YkdVZ2FWUjFibVZ6SUZOMGIzSmxJRU5sY25ScFptbGpZWFJwYjI0Z1FYVjBhRzl5YVhSNU1CNFhEVEE1TURZeE5USXlNRFUxTmxvWERURTBNRFl4TkRJeU1EVTFObG93WkRFak1DRUdBMVVFQXd3YVVIVnlZMmhoYzJWU1pXTmxhWEIwUTJWeWRHbG1hV05oZEdVeEd6QVpCZ05WQkFzTUVrRndjR3hsSUdsVWRXNWxjeUJUZEc5eVpURVRNQkVHQTFVRUNnd0tRWEJ3YkdVZ1NXNWpMakVMTUFrR0ExVUVCaE1DVlZNd2daOHdEUVlKS29aSWh2Y05BUUVCQlFBRGdZMEFNSUdKQW9HQkFNclJqRjJjdDRJclNkaVRDaGFJMGc4cHd2L2NtSHM4cC9Sd1YvcnQvOTFYS1ZoTmw0WElCaW1LalFRTmZnSHNEczZ5anUrK0RyS0pFN3VLc3BoTWRkS1lmRkU1ckdYc0FkQkVqQndSSXhleFRldngzSExFRkdBdDFtb0t4NTA5ZGh4dGlJZERnSnYyWWFWczQ5QjB1SnZOZHk2U01xTk5MSHNETHpEUzlvWkhBZ01CQUFHamNqQndNQXdHQTFVZEV3RUIvd1FDTUFBd0h3WURWUjBqQkJnd0ZvQVVOaDNvNHAyQzBnRVl0VEpyRHRkREM1RllRem93RGdZRFZSMFBBUUgvQkFRREFnZUFNQjBHQTFVZERnUVdCQlNwZzRQeUdVakZQaEpYQ0JUTXphTittVjhrOVRBUUJnb3Foa2lHOTJOa0JnVUJCQUlGQURBTkJna3Foa2lHOXcwQkFRVUZBQU9DQVFFQUVhU2JQanRtTjRDL0lCM1FFcEszMlJ4YWNDRFhkVlhBZVZSZVM1RmFaeGMrdDg4cFFQOTNCaUF4dmRXLzNlVFNNR1k1RmJlQVlMM2V0cVA1Z204d3JGb2pYMGlreVZSU3RRKy9BUTBLRWp0cUIwN2tMczlRVWU4Y3pSOFVHZmRNMUV1bVYvVWd2RGQ0TndOWXhMUU1nNFdUUWZna1FRVnk4R1had1ZIZ2JFL1VDNlk3MDUzcEdYQms1MU5QTTN3b3hoZDNnU1JMdlhqK2xvSHNTdGNURXFlOXBCRHBtRzUrc2s0dHcrR0szR01lRU41LytlMVFUOW5wL0tsMW5qK2FCdzdDMHhzeTBiRm5hQWQxY1NTNnhkb3J5L0NVdk02Z3RLc21uT09kcVRlc2JwMGJzOHNuNldxczBDOWRnY3hSSHVPTVoydG04bnBMVW03YXJnT1N6UT09IjsKCSJwdXJjaGFzZS1pbmZvIiA9ICJld29KSW05eWFXZHBibUZzTFhCMWNtTm9ZWE5sTFdSaGRHVXRjSE4wSWlBOUlDSXlNREV5TFRBMExUTXdJREE0T2pBMU9qVTFJRUZ0WlhKcFkyRXZURzl6WDBGdVoyVnNaWE1pT3dvSkltOXlhV2RwYm1Gc0xYUnlZVzV6WVdOMGFXOXVMV2xrSWlBOUlDSXhNREF3TURBd01EUTJNVGM0T0RFM0lqc0tDU0ppZG5KeklpQTlJQ0l5TURFeU1EUXlOeUk3Q2draWRISmhibk5oWTNScGIyNHRhV1FpSUQwZ0lqRXdNREF3TURBd05EWXhOemc0TVRjaU93b0pJbkYxWVc1MGFYUjVJaUE5SUNJeElqc0tDU0p2Y21sbmFXNWhiQzF3ZFhKamFHRnpaUzFrWVhSbExXMXpJaUE5SUNJeE16TTFOems0TXpVMU9EWTRJanNLQ1NKd2NtOWtkV04wTFdsa0lpQTlJQ0pqYjIwdWJXbHVaRzF2WW1Gd2NDNWtiM2R1Ykc5aFpDSTdDZ2tpYVhSbGJTMXBaQ0lnUFNBaU5USXhNVEk1T0RFeUlqc0tDU0ppYVdRaUlEMGdJbU52YlM1dGFXNWtiVzlpWVhCd0xrMXBibVJOYjJJaU93b0pJbkIxY21Ob1lYTmxMV1JoZEdVdGJYTWlJRDBnSWpFek16VTNPVGd6TlRVNE5qZ2lPd29KSW5CMWNtTm9ZWE5sTFdSaGRHVWlJRDBnSWpJd01USXRNRFF0TXpBZ01UVTZNRFU2TlRVZ1JYUmpMMGROVkNJN0Nna2ljSFZ5WTJoaGMyVXRaR0YwWlMxd2MzUWlJRDBnSWpJd01USXRNRFF0TXpBZ01EZzZNRFU2TlRVZ1FXMWxjbWxqWVM5TWIzTmZRVzVuWld4bGN5STdDZ2tpYjNKcFoybHVZV3d0Y0hWeVkyaGhjMlV0WkdGMFpTSWdQU0FpTWpBeE1pMHdOQzB6TUNBeE5Ub3dOVG8xTlNCRmRHTXZSMDFVSWpzS2ZRPT0iOwoJImVudmlyb25tZW50IiA9ICJTYW5kYm94IjsKCSJwb2QiID0gIjEwMCI7Cgkic2lnbmluZy1zdGF0dXMiID0gIjAiOwp9';
            fwrite($f, print_r($data, true) . "\n");


            //return [""$receiptBase64Data;

            try {
                $response = $validator->validate($receiptBase64Data, "63baa92164f4400699f78f32dded316f");
            } catch (Exception $e) {
                throw new BadRequestHttpException($e->getMessage());
            }

            if ($response->isValid()) {
                $data = $response->getReceipt();
                fwrite($f, print_r($data, true) . "\n");
                //print_r($data);
                $products = $data["in_app"];
                foreach ($products as $p) {
                    $transactionID = (string)ArrayHelper::getValue($p, "transaction_id");
                    $productID = (string)ArrayHelper::getValue($p, "product_id");
                    $payment = Payment::find()->where(["operation_id" => $transactionID])->one();
                    if (!$payment) {
                        $payment = new Payment();
                        $payment->user_id = $user->id;
                        $payment->tm = new Expression('NOW()');
                        if (ArrayHelper::getValue($data, "receipt_type") == "ProductionSandbox") {
                            $payment->type_id = Payment::TYPE_TESTAPPLE;
                        } else {
                            $payment->type_id = Payment::TYPE_APPLE;
                        }
                        $payment->operation_label = $productID;
                        $payment->operation_id = $transactionID;
                        $payment->source = $source;
                        $sum = 0;
                        $checks = 0;
                        if (preg_match("/com\.wcaller\.Wcaller\.search(\d+)/", $productID, $m)) {
                            switch ($m[1]) {
                                case 1:
                                    $sum = 149;
                                    break;
                                case 10:
                                    $sum = 749;
                                    break;
                                case 100:
                                    $sum = 3490;
                                    break;
                                case 300:
                                    $sum = 8990;
                                    break;
                                case 30:
                                    $sum = 1390;
                                    break;
                            }
                            $checks = $m[1];
                        }
                        $isSubscribe = false;
                        if (preg_match("/com\.wcaller\.Wcaller\.sub\.month\.(\d+)/", $productID, $m)) {
                            switch ($m[1]) {
                                case 0:
                                    $sum = 2750;
                                    break;
                                case 10:
                                    $sum = 199;
                                    break;
                                case 15:
                                    $sum = 299;
                                    break;
                                case 50:
                                    $sum = 499;
                                    break;
                                case 999:
                                    $sum = 1690;
                            }
                            $checks = $m[1];
                            $isSubscribe = true;
                        }
                        if (preg_match("/com\.wcaller\.Wcaller\.sub\.6month\.(\d+)/", $productID, $m)) {
                            switch ($m[1]) {
                                case 0:
                                    $sum = 6190;
                                    break;
                            }
                            $checks = $m[1];
                            $isSubscribe = true;
                        }
                        if (preg_match("/com\.wcaller\.Wcaller\.sub\.week\.(\d+)/", $productID, $m)) {
                            switch ($m[1]) {
                                case 0:
                                    $sum = 499;
                                    break;
                                case 999:
                                    $sum = 249;
                                    break;
                            }
                            $checks = $m[1];
                            $isSubscribe = true;
                        }
                        if($checks == 0 || $checks == 999) $checks = -1;

                        $payment->sum = $sum;
                        $payment->amount = $sum * 0.59;
                        if ($payment->save()) {
                            if ($isSubscribe) {
                                $sub = new UserSub();
                                $sub->user_id = $user->id;
                                $sub->transaction_id = ArrayHelper::getValue($p, "transaction_id");
                                $sub->original_transaction_id = ArrayHelper::getValue($p, "original_transaction_id");
                                $sub->tm_purchase = \Yii::$app->formatter->asDatetime(ArrayHelper::getValue($p, "purchase_date"), "yyyy-MM-dd HH:mm:ss");
                                $sub->tm_expires = \Yii::$app->formatter->asDatetime(ArrayHelper::getValue($p, "expires_date"), "yyyy-MM-dd HH:mm:ss");
                                $sub->status = ArrayHelper::getValue($p, "is_trial_period")?0:1;
                                $sub->save();
                            }
                            $user->checks += $checks;
                            $user->save();
                        }
                    } else {
                        if($payment->user_id != $user->id) {
                            $payment->user_id = $user->id;
                            $payment->save();
                        }
                        $sub = UserSub::find()->where(["transaction_id" => ArrayHelper::getValue($p, "transaction_id")])->one();
                        if($sub && $sub->user_id != $user->id) {
                            $sub->user_id = $user->id;
                            $sub->save();

                            $checks = null;
                            if (preg_match("/com\.wcaller\.Wcaller\.sub\.month\.(\d+)/", $productID, $m)) {
                                switch ($m[1]) {
                                    case 0:
                                        $sum = 2750;
                                        break;
                                    case 10:
                                        $sum = 199;
                                        break;
                                    case 15:
                                        $sum = 299;
                                        break;
                                    case 50:
                                        $sum = 499;
                                        break;
                                    case 999:
                                        $sum = 1690;
                                }
                                $checks = $m[1];
                                $isSubscribe = true;
                            }
                            if (preg_match("/com\.wcaller\.Wcaller\.sub\.6month\.(\d+)/", $productID, $m)) {
                                switch ($m[1]) {
                                    case 0:
                                        $sum = 6190;
                                        break;
                                }
                                $checks = $m[1];
                                $isSubscribe = true;
                            }
                            if (preg_match("/com\.wcaller\.Wcaller\.sub\.week\.(\d+)/", $productID, $m)) {
                                switch ($m[1]) {
                                    case 0:
                                        $sum = 499;
                                        break;
                                    case 999:
                                        $sum = 249;
                                        break;
                                }
                                $checks = $m[1];
                                $isSubscribe = true;
                            }
                            if($checks == 0 || $checks == 999) $checks = -1;
                            $user->checks = $checks;
                            $user->save();
                        }
                    }
                }
                //product_id
                //echo 'Receipt is valid.' . PHP_EOL;
                //echo 'Receipt data = ' . print_r($response->getReceipt()) . PHP_EOL;
            } else {
                fwrite($f, "invalid data\n");
            }
            fclose($f);
        }

        return [
            "id" => $user->id,
            "balance" => $user->balance,
            "checks" => $user->checks
        ];
    }
}