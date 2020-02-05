<?php

namespace app\controllers;

use app\models\Retargeting;
use app\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\db\Expression;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;

class RetargetingController extends Controller
{

    /**
     * @param $uuid
     * @param $user_id
     * подсчитываем сколько пользователей открыло письмо
     */
    public function actionPic($uuid)
    {
        //отмечаем письмо как прочитано
        if ($uuid) {
            $retargeting = Retargeting::find()->where(["uuid" => $uuid, "status" => 1])->one();

            if (!is_null($retargeting)) {
                $retargeting->status = 2;
                $retargeting->tm_read = new Expression('NOW()');
                $retargeting->save();
            }
        }

        //формируем прозрачную картинку gif размером 1 x 1 pix и выводи в браузер
        $img = ImageCreateTrueColor(1,1);

        \Yii::$app->response->format = Response::FORMAT_RAW;
        \Yii::$app->response->headers->set('Content-Type', 'image/gif');

        return imagegif($img);
    }

    /**
     * @return Response
     * @throws NotFoundHttpException
     * подсчитываем количество кликов по ссылке
     */
    public function actionRedirect($uuid)
    {

        if ($uuid) {
            $retargeting = Retargeting::find()->where(["uuid" => $uuid])->one();

            if (!is_null($retargeting)) {

                if ($retargeting->status != 2) throw new ForbiddenHttpException("Нет доступа");

                $retargeting->status = 3;
                $retargeting->tm_click = new Expression('NOW()');
                $retargeting->save();

                $user = User::find()->where(['id' => $retargeting->user_id])->one();
                $user->checks = $user->checks + 1;
                $user->save();

                return $this->redirect('http://kto.lol/get/' . $uuid);

            } else {
                throw new NotFoundHttpException("Страница не найдена");
            }
        } else {
            throw new NotFoundHttpException("Страница не найдена");
        }

    }
}
