<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use app\models\Telegram;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

class AccountsController extends AdminController
{
    public function actionTelegram()
    {
        $model = new Telegram();

        $dataProvider = new ActiveDataProvider([
            'query' => Telegram::find(),
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        if (Yii::$app->getRequest()->getIsPost()) {
            $model->load(Yii::$app->getRequest()->post());

            if (!$model->validate()) {
                return $this->render('telegram', compact('model', 'dataProvider'));
            }

            $model->save();
            $this->refresh();
        }

        return $this->render('telegram', compact('model', 'dataProvider'));
    }

    public function actionDeleteTelegram($id)
    {
        if ($instance = Telegram::findOne($id)) $instance->delete();
        if (!$instance) throw new NotFoundHttpException('Инстанс не найден.');
        $referrer = Yii::$app->getRequest()->getReferrer();
        $url = $referrer ? $referrer : Url::to(['accounts/telegram']);
        return $this->redirect($url);
    }
}
