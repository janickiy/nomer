<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use app\models\Token;

class TokensController extends AdminController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'except' => ['query'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            /* @var $identity \app\models\User */
                            $identity = \Yii::$app->getUser()->getIdentity();
                            return $identity->is_admin;
                        }
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $model = new Token();

        $dataProvider = new ActiveDataProvider([
            'query' => Token::find(),
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        if (Yii::$app->getRequest()->getIsPost()) {
            $model->load(Yii::$app->getRequest()->post());

            if (!$model->validate()) {
                return $this->render('index', compact('model', 'dataProvider'));
            }

            $model->save();
            $this->refresh();
        }

        return $this->render('index', compact('model', 'dataProvider'));
    }

    public function actionDelete($id)
    {
        if ($domain = Token::findOne($id)) $domain->delete();
        if (!$domain) throw new NotFoundHttpException('Токен не найден.');
        $referrer = Yii::$app->getRequest()->getReferrer();
        $url = $referrer ? $referrer : Url::to(['tokens/index']);
        return $this->redirect($url);
    }

    public function actionQuery($server, $type)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return ArrayHelper::getColumn(Token::find()
            ->where(['type' => $type])
            ->andWhere(['server_id' => $server])
            ->andWhere(['status' => Token::STATUS_ACTIVE])
            ->all(), 'token');
    }
}
