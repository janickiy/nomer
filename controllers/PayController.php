<?php
namespace app\controllers;

use app\models\BlockPhone;
use app\models\forms\WmForm;
use app\models\Payment;
use app\models\PhoneRequest;
use app\models\Repost;
use app\models\Site;
use app\models\Ticket;
use app\models\User;
use app\models\WebmoneyOrder;
use Imagick;
use ImagickDraw;
use ImagickPixel;
use yii\db\Exception;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

class PayController extends Controller {

    public $enableCsrfValidation = false;

    public function actionIndex() {
        if(\Yii::$app->getUser()->isGuest) return $this->goHome();

        $hasRepost = Repost::find()->where(["user_id" => \Yii::$app->getUser()->getId()])->count(1);

        return $this->render("index", ["hasRepost" => $hasRepost]);
    }

    public function actionSuccess() {
        return $this->render("success");
    }

    public function actionFindPhoneSuccess() {
        return $this->render("find-phone-success");
    }

    public function actionPaypal() {
        $f = fopen(\Yii::getAlias('@runtime')."/paypal.log", 'a+');
        fwrite($f, print_r(\Yii::$app->request->post(), true)."\n\n");
        fwrite($f, print_r(\Yii::$app->request->get(), true)."\n\n");
        fclose($f);
    }

    public function actionCouponCheck() {
        $uniquecode = \Yii::$app->request->get("uniquecode");

        $data = [
            "id_seller" => "729622",
            "unique_code" => $uniquecode,
            "sign" => md5("729622:".$uniquecode.":F58F3834A6")
        ];
        $data = Json::encode($data);

        $ch = curl_init("https://www.oplata.info/xml/check_unique_code.asp");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($ch);
        curl_close($ch);

	$f = fopen(\Yii::getAlias('@runtime')."/ccc.log", "a+");
	fwrite($f, $response."\n\n");
	fclose($f);

        $response = Json::decode($response);
        if(ArrayHelper::getValue($response, "retval") == 0) {
            $base64params = ArrayHelper::getValue($response, "query_string");
            $base64params = base64_decode($base64params);
            parse_str($base64params, $output);

            $checks = ArrayHelper::getValue($response, "cnt_goods");

            $sum = ArrayHelper::getValue($response, "amount");
            $sum = str_replace(",", ".", $sum);
	        $amount = $sum - ($sum * 0.015);

            $payment = Payment::find()->where(["type_id" => Payment::TYPE_COUPON, "operation_label" => $uniquecode])->one();
            if(!$payment) {
                $payment = new Payment();
                $payment->user_id           = (int)$output["user_id"];
                $payment->sum               = $sum;
                $payment->site_id           = (int)ArrayHelper::getValue($output, "site_id", 1);
                $payment->amount            = $amount;
                $payment->tm                = date("Y-m-d H:i:s", strtotime(ArrayHelper::getValue($response, "date_pay")));
                $payment->operation_label   = (string)ArrayHelper::getValue($response, "unique_code");
                $payment->operation_id      = (string)ArrayHelper::getValue($response, "inv");
                $payment->type_id           = Payment::TYPE_COUPON;
                $payment->save();

                if ($payment->user_id) {
                    /* @var $user \app\models\User */
			$user = User::find()->where(["id" => $payment->user_id])->one();
                    $user->addBalance($sum, $amount, true, $payment->site_id);
                }
            }

        }

        return $this->redirect(["pay/success"]);
    }

    public function actionRepost() {
        if(\Yii::$app->getUser()->isGuest) return $this->goHome();
        $hasRepost = Repost::find()->where(["user_id" => \Yii::$app->getUser()->getId()])->count(1);

        return $this->render("repost", [
            "hasRepost" => $hasRepost
        ]);
    }

    public function actionCheckRepost() {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $site = Site::find()->where(["name" => $_SERVER["HTTP_HOST"]])->one();

        $response = file_get_contents("https://api.vk.com/method/likes.getList?type=sitepage&owner_id=".$site->vk_id."&item_id=".\Yii::$app->getUser()->getId()."&filter=copies&v=4.93");

        $response = Json::decode($response);
        
        $vkID = ArrayHelper::getValue($response, ["response", "items", 0], false);

        $responseFriends = file_get_contents("https://api.vk.com/method/friends.get?user_id=".$vkID."&v=5.8");
        $responseFriends = Json::decode($responseFriends);
        $friends = ArrayHelper::getValue($responseFriends, ["response", "count"], 0);

        if($vkID and $friends > 20) {
            $repost = Repost::find()->where(["vk_id" => $vkID])->one();
            if(!$repost) {
                $repost = new Repost();
                $repost->user_id = \Yii::$app->getUser()->getId();
                $repost->site_id = $site->id;
                $repost->vk_id = $vkID;
                $repost->tm = new Expression("NOW()");
                if($repost->save()) {
                    $user = User::find()->where(["id" => $repost->user_id])->one();
                    $user->checks += 2;
                    $user->save();
                    return ["success" => 1];
                }
            }
        }

        return ["success" => 0];
    }

    public function actionQiwi() {
        //if (\Yii::$app->getUser()->isGuest) return $this->goHome();
        return $this->render('qiwi');
    }

    public function actionQiwiBlock()
    {
        return $this->render('qiwi_block');
    }

    public function actionPaymentwallResult()
    {
        $f = fopen(\Yii::getAlias('@runtime') . '/paymentwall.txt', "a+");
        fwrite($f, Json::encode(\Yii::$app->request->post()));
        fwrite($f, Json::encode(\Yii::$app->request->get()));
        fclose($f);
    }

    public function actionWebmoneyResult() {
        $post = \Yii::$app->request->post();
        \Yii::$app->response->statusCode = 200;
        if(!count($post)) {
            echo "YES"; die();
        }

        $f = fopen(\Yii::getAlias('@runtime').'/wm.log', 'a+');
        fwrite($f, Json::encode(\Yii::$app->request->post())."\n\n");


        $wmForm = new WmForm;
        $wmForm->LMI_PAYEE_PURSE = \Yii::$app->request->post('LMI_PAYEE_PURSE');
        $wmForm->LMI_PAYMENT_AMOUNT = \Yii::$app->request->post('LMI_PAYMENT_AMOUNT');
        $wmForm->LMI_PAYMENT_NO = \Yii::$app->request->post('LMI_PAYMENT_NO');
        $wmForm->LMI_MODE = \Yii::$app->request->post('LMI_MODE');
        $wmForm->LMI_SYS_INVS_NO = \Yii::$app->request->post('LMI_SYS_INVS_NO');
        if(trim($wmForm->LMI_SYS_INVS_NO) == '') throw new BadRequestHttpException('Error');
        $wmForm->LMI_SYS_TRANS_NO = \Yii::$app->request->post('LMI_SYS_TRANS_NO');
        if(trim($wmForm->LMI_SYS_TRANS_NO) == '') throw new BadRequestHttpException('Error');
        $wmForm->LMI_SYS_TRANS_DATE = \Yii::$app->request->post('LMI_SYS_TRANS_DATE');
        $wmForm->LMI_SECRET_KEY = \Yii::$app->request->post('LMI_SECRET_KEY');
        $wmForm->LMI_PAYER_PURSE = \Yii::$app->request->post('LMI_PAYER_PURSE');
        $wmForm->LMI_PAYER_WM = \Yii::$app->request->post('LMI_PAYER_WM');
        $wmForm->LMI_HASH = \Yii::$app->request->post('LMI_HASH');

        fwrite($f, "WMFORM BEFORE VALIDATE\n");

        if($wmForm->validate()){
            fwrite($f, "WMFORM SUCCESS VALIDATE\n");
            $order = WebmoneyOrder::find()->where(["id" => (int)$wmForm->LMI_PAYMENT_NO, "status" => 0])->one();
            if(!$order) die();

            if($order->user_id > 0) {
                $user = User::find()->where(["id" => $order->user_id])->one();
            } else {
                $findPhone = PhoneRequest::find()->where(["id" => -$order->user_id])->one();
                $user = User::find()->where(["id" => $findPhone->user_id])->one();

                $ticket = new Ticket();
                $ticket->detachBehavior("user_id");
                $ticket->user_id = $user->id;
                $ticket->site_id = $order->site_id;
                $ticket->subject_id = 1;
                $ticket->text = $findPhone->data;
                $ticket->subject = "Запрос на поиск номера телефона";
                $ticket->status = 0;
                $ticket->is_payed = true;
                $ticket->tm_create = new Expression('NOW()');
                $ticket->save(false);
            }


            fwrite($f, "WMFORM ORDER ".$order->id."\n");

            $sum = \Yii::$app->request->post('LMI_PAYMENT_AMOUNT');

            $payment                    = new Payment();
            $payment->site_id           = $order->site_id;
            $payment->user_id           = $user->id;
            $payment->sum               = $sum;
            $payment->amount            = $sum;
            $payment->tm                = new Expression('NOW()');
            $payment->operation_label   = (string)\Yii::$app->request->post('LMI_SYS_INVS_NO');
            $payment->operation_id      = (string)\Yii::$app->request->post('LMI_SYS_TRANS_NO');
            $payment->type_id           = Payment::TYPE_WEBMONEY;
            if(!$payment->save()) {
                fwrite($f, Json::encode($payment->getErrors()));
            }

            if ($payment->user_id) {
                /* @var $user \app\models\User */
                $user = User::find()->where(['id' => $payment->user_id])->one();
                $user->addBalance($sum, $sum, true, $payment->site_id);
            }

            $order->status = 1;
            $order->save();

            echo 'OK';
        } else {
            fwrite($f, "WMFORM FAIL VALIDATE\n");
            fwrite($f, Json::encode($wmForm->getErrors()));
        }

        fclose($f);
        die();
    }

    public function actionResult() {
        $f = fopen(\Yii::getAlias('@runtime').'/log.txt', "a+");
        fwrite($f, Json::encode(\Yii::$app->request->post())."\n\n");
        fwrite($f, Json::encode(\Yii::$app->request->get())."\n\n");
        fclose($f);

        $post = \Yii::$app->request->post();

        $label = explode('-', ArrayHelper::getValue($post, 'label'));

        $blockPayment = $label[0] == 'block';

        $sum = ArrayHelper::getValue($post, 'withdraw_amount');
        $notification_type = (string) ArrayHelper::getValue($post, 'notification_type');

        if ($blockPayment) {
            $blockedPhone = BlockPhone::find()->where(['phone' => $label[1], 'status' => BlockPhone::STATUS_CONFIRMED])->one();

            if ($sum >= 299 && $blockedPhone) {
                $blockedPhone->status = BlockPhone::STATUS_PAID;
                $blockedPhone->save();
            }

            $payment = Payment::find()->where(["operation_id" => (string)ArrayHelper::getValue($post, "operation_id")])->one();
            if ($payment) return '';

            $userID = $label[2] == 0 ? null : $label[2];
            $siteID = $label[3];
        } else {
            $payment = Payment::find()->where(["operation_id" => (string)ArrayHelper::getValue($post, "operation_id")])->one();
            if ($payment) return '';

            $userID = (int)$label[0];
            $siteID = 0;

            if (isset($label[1])) {
                $siteID = (int)$label[1];
            }
        }

        $payment                    = new Payment();
        $payment->user_id           = $userID;
        $payment->sum               = $sum;
        $payment->site_id           = $siteID;
        $payment->amount            = ArrayHelper::getValue($post, "amount");
        $payment->tm                = date("Y-m-d H:i:s", strtotime(ArrayHelper::getValue($post, "datetime")));
        $payment->operation_label   = (string)ArrayHelper::getValue($post, "operation_label");
        $payment->operation_id      = (string)ArrayHelper::getValue($post, "operation_id");
        $payment->type_id           = $notification_type=="card-incoming"?Payment::TYPE_YANDEX:Payment::TYPE_YANDEX_WALLET;
        $payment->save();

        if($payment->sum == 1000) {
            $findPhone = PhoneRequest::find()->where(["user_id" => $payment->user_id])->orderBy(["id" => SORT_DESC])->one();
            $ticket = new Ticket();
            $ticket->detachBehavior("user_id");
            $ticket->user_id = $userID;
            $ticket->site_id = $siteID;
            $ticket->subject_id = 1;
            $ticket->text = $findPhone->data;
            $ticket->subject = "Запрос на поиск номера телефона";
            $ticket->status = 0;
            $ticket->is_payed = true;
            $ticket->tm_create = new Expression('NOW()');
            $ticket->save(false);
        } else {
            if (!$blockPayment && $payment->user_id) {
                /* @var $user \app\models\User */
                $user = User::find()->where(['id' => $payment->user_id])->one();
                $user->addBalance($sum, $payment->amount, true, $payment->site_id);
            }
        }
    }

    public function actionFindPhoneConfirm() {
        $id = \Yii::$app->request->get("id");
        $request = PhoneRequest::find()->where(["id" => $id, "user_id" => \Yii::$app->getUser()->getId()])->one();
        if(!$request) {
            throw new ForbiddenHttpException();
        }

        return $this->render("find-phone-confirm", ["id" => $id]);
    }

    public function actionFindPhone() {
        $id = \Yii::$app->request->get("id");
        $request = PhoneRequest::find()->where(["id" => $id, "user_id" => \Yii::$app->getUser()->getId()])->one();
        if(!$request) {
            throw new ForbiddenHttpException();
        }

        $dataType = $data = null;
        if(preg_match('/@/', $request->data)) {
            $dataType = "email";
            $data = $request->data;
        } elseif(preg_match('/vk\.com\/(.+)/', $request->data, $m)) {
            $dataType = "vk";
            $vkResponse = @file_get_contents("https://api.vk.com/method/users.get?user_ids=".$m[1]."&fields=photo_max,photo_max_orig");
            $vkResponse = Json::decode($vkResponse);
            $data = ArrayHelper::getValue($vkResponse, ["response", 0]);
        } elseif(preg_match('/facebook\.com/', $request->data)) {
            $fbId = preg_replace('[\D]', '', $request->data);
            $dataType = "fb";
            $fbResponse = @file_get_contents("https://graph.facebook.com/".$fbId."?fields=first_name,last_name&access_token=223417934354442|uoEzUVtKfO6Y-txtcgT8i4bzRG8&locale=ru_RU");
            $fbResponse = Json::decode($fbResponse);
            $data = $fbResponse;
            $data["photo"] = "http://graph.facebook.com/".$fbId."/picture?width=400&height=400";
        } elseif(preg_match('/instagram/', $request->data)) {
            $dataType = "instagram";
            $data = $request->data;
        }

        return $this->render("find-phone", [
            "id"        => $id,
            "request"   => $request,
            "dataType"  => $dataType,
            "data"      => $data
        ]);
    }

    public function actionQiwiCheck()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $value = \Yii::$app->getRequest()->get('value');

        $payment = Payment::find()
            ->where(['type_id' => [Payment::TYPE_QIWI, Payment::TYPE_QIWI_TERMINAL]])
            ->andWhere(['or', ['operation_id' => $value], ['operation_label' => $value]])
            ->one();

        if (!$payment) return ['code' => 0];
        if ($payment->user_id === null) return ['response' => 3];
        if ($payment->user_id != \Yii::$app->getUser()->getId()) return ['code' => 2];
        return ['code' => 1];
    }

    public function actionMethods() {
        if(\Yii::$app->getUser()->isGuest) return $this->goHome();

        return $this->render("methods");
    }

    public function actionMethods2() {
        if(\Yii::$app->getUser()->isGuest) return $this->goHome();

        return $this->render("methods2");
    }
}
?>
