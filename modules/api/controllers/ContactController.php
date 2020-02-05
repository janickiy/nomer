<?php
namespace app\modules\api\controllers;

use app\models\Ticket;
use app\models\User;
use Yii;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;

class ContactController extends Controller {

    public $enableCsrfValidation = false;

    public function actionIndex() {
        $uuid = Yii::$app->getRequest()->getHeaders()->get('uuid', false);
        if(!$uuid) {
            throw new BadRequestHttpException();
        }

        $user = User::find()->where(["uuid" => $uuid])->one();

        $ticket = new Ticket();
        $ticket->detachBehavior("user_id");
        $ticket->user_id = $user->id;

        $rawBody = \Yii::$app->request->getRawBody();

        $email = \Yii::$app->request->post("email", null);
        if($email) {
            $message = \Yii::$app->request->post("message");
        } else {
            $data = Json::decode($rawBody);
            $message = ArrayHelper::getValue($data,"message");
            $email = ArrayHelper::getValue($data,"email");
        }

        $ticket->text = $message."\n\n".$email;
        $ticket->tm_create = new Expression('NOW()');
        $ticket->subject_id = 1;
        $ticket->subject = "Сообщение из iOS приложения";
        $ticket->save(false);

        return ["success" => \Yii::$app->mailer->compose()
            ->setTextBody("E-mail: ".$email."\n\n\n".$message)
            ->setFrom('noreply@'.\Yii::$app->name)
            ->setTo("support@nomer.io")
            ->setSubject(\Yii::$app->name." - обратная связь")
            ->send()];

    }
}