<?php

namespace app\commands;

use yii\console\Controller;
use app\models\Payment;
use app\models\Retargeting;
use app\models\User;
use yii\db\Expression;

class DaemonController extends Controller
{
    /**
     * создаём очередь на отправку
     */
    public function actionQueueRetargeting()
    {
        $max_date = date("Y-m-d 00:00:00", strtotime("-1 day"));

        $users = User::find()
            ->select(['users.*', 'sum(payments.sum)', 'max(email_tokents.tm_create)'])
            ->innerJoin('payments', 'payments.user_id=users.id')
            ->join('LEFT JOIN', 'requests', 'requests.user_id=users.id')
            ->join('LEFT JOIN', 'email_tokents', 'email_tokents.user_id=users.id')
            ->orderBy(['requests.id' => SORT_DESC])
            ->groupBy(['users.id', 'requests.id', 'email_tokents.tm_create'])
            ->having('sum(sum) > 0')
            ->having("max(email_tokents.tm_create) < '" . $max_date . "' OR COUNT(email_tokents.id) = 0")
            ->limit(100)
            ->all();

        //var_dump($users);
        //exit;

        if (!is_null($users)) {

            foreach ($users as $user) {
                $date1 = date("Y-m-d") . '09:00';
                $date2 = date("Y-m-d") . '19:00';

                $retargeting = new Retargeting();
                $retargeting->uuid = md5($user->id . time());
                $retargeting->status = 0;
                $retargeting->tm_create = new Expression('NOW()');
                $retargeting->tm_send = Retargeting::random_date_in_range($date1, $date2);
                $retargeting->user_id = $user->id;
                $retargeting->save();
            }
        }
    }

    /**
     * длеаем рассылку
     */
    public function actionSendMsg()
    {
        $retargetings = Retargeting::find()->joinWith("user")
                        ->joinWith(['user'])
                        ->where(["email_tokents.status" => 0])
                    ->all();

        if (!is_null($retargetings)) {
            foreach ($retargetings as $retargeting) {

                $result = \Yii::$app->mailer->compose()
                    ->setTextBody(\Yii::t('email','msg'))
                    ->setFrom('noreply@' . \Yii::$app->name)
                    ->setTo($retargeting->user->email)
                    ->setSubject(\Yii::t('email','subject'))
                    ->send();

                $retarg = Retargeting::find()->where(["uuid" => $retargeting->uuid, "user_id" => $retargeting->user_id, "status" => 0])->one();

                if (!is_null($retarg)) {
                    $retargeting->status = $result ? 1 : 4;
                    $retargeting->tm_send = new Expression('NOW()');

                    if ($result === false) $retarg->descr = 'Ошибка при отправке письма';

                    $retarg->save();
                }

            }
        }
    }
}
