<?php

namespace app\modules\admin\controllers;

use app\models\UserAuthLog;
use Yii;
use app\models\Payment;
use app\models\SearchRequest;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\data\Sort;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class UsersController extends AdminController
{
    public function actionIndex()
    {
        $tm_start = Yii::$app->getRequest()->get('tm_start', date('Y-m-d'));
        $tm_end = Yii::$app->getRequest()->get('tm_end', date('Y-m-d'));
        $email = Yii::$app->getRequest()->get('email');
        $isVIP = (bool) Yii::$app->getRequest()->get('is_vip', 0);
        $isAdmin = (bool) Yii::$app->getRequest()->get('is_admin', 0);
        $withChecks = (bool) Yii::$app->getRequest()->get('with_checks', 0);

        $registrationsQuery = (new Query())
            ->select(['auth.source', 'count(users.id)'])
            ->from('users')
            ->join('LEFT JOIN', 'auth', 'auth.user_id = users.id')
            ->groupBy('auth.source')
            ->orderBy(['auth.source' => SORT_ASC]);

        if ($isVIP) $registrationsQuery->andWhere(['users.is_vip' => true]);
        if ($isAdmin) $registrationsQuery->andWhere(['users.is_admin' => true]);

        if (!$isVIP && !$isAdmin && !$withChecks) {
            $registrationsQuery->andWhere(['>=', 'users.tm_create', $tm_start . ' 00:00:00'])
                ->andWhere(['<=', 'users.tm_create', $tm_end . ' 23:59:59']);
        }

        if ($withChecks) $registrationsQuery->andWhere(['>', 'users.checks', 0]);

        $registrations = $registrationsQuery->all();

        $registrationConfirmsQuery = (new Query())
            ->select(['auth.source', 'count(users.id)'])
            ->from('users')
            ->join('LEFT JOIN', 'auth', 'auth.user_id = users.id')
            ->andWhere(['is_confirm' => true])
            ->groupBy('auth.source')
            ->orderBy(['auth.source' => SORT_ASC])
            ->indexBy('source');

        if ($isVIP) $registrationConfirmsQuery->andWhere(['users.is_vip' => true]);
        if ($isAdmin) $registrationConfirmsQuery->andWhere(['users.is_admin' => true]);

        if (!$isVIP && !$isAdmin && !$withChecks) {
            $registrationConfirmsQuery->andWhere(['>=', 'users.tm_create', $tm_start . ' 00:00:00'])
                ->andWhere(['<=', 'users.tm_create', $tm_end . ' 23:59:59']);
        }

        if ($withChecks) $registrationConfirmsQuery->andWhere(['>', 'users.checks', 0]);

        $registrationConfirms = $registrationConfirmsQuery->all();

        $query = User::find();

        if ($email) $query->andWhere(['ilike', 'email', $email]);
        if ($isVIP) $query->andWhere(['is_vip' => true]);
        if ($isAdmin) $query->andWhere(['is_admin' => true]);

        if (!$email && !$isVIP && !$isAdmin && !$withChecks) {
            $query
                ->where(['>=', 'tm_create', $tm_start . ' 00:00:00'])
                ->andWhere(['<=', 'tm_create', $tm_end . ' 23:59:59']);
        }

        if ($withChecks) $query->andWhere(['>', 'checks', 0]);

        $phones = \Yii::$app->db->createCommand("select count(1) from users where tm_create >='".$tm_start." 00:00:00' AND tm_create <= '".$tm_end." 23:59:59' and exists (select * from regexp_matches(users.email, '^[0-9]{11,12}$'))")->queryScalar();
        $registrations[] = ["source" => "phone", "count" => $phones];

        $ios = \Yii::$app->db->createCommand("select count(1) from users where tm_create >='".$tm_start." 00:00:00' AND tm_create <= '".$tm_end." 23:59:59' and email is null and exists (select * from regexp_matches(users.uuid, '^[0-9A-Z-]{36}$'))")->queryScalar();
        $registrations[] = ["source" => "iOS", "count" => $ios];

        $android = \Yii::$app->db->createCommand("select count(1) from users where tm_create >='".$tm_start." 00:00:00' AND tm_create <= '".$tm_end." 23:59:59' and email is not null and exists (select * from regexp_matches(users.uuid, '^[0-9A-Za-z-]{36}$'))")->queryScalar();
        $registrations[] = ["source" => "Android", "count" => $android];

        $dataProvider = new ActiveDataProvider([
            'query' => $query->with(['requests']),
            'sort' => new Sort([
                'attributes' => [
                    'id',
                    'checks' => [
                        'default' => SORT_DESC
                    ]
                ],
                'defaultOrder' => [
                    'id' => SORT_DESC
                ]
            ])
        ]);

        return $this->render('index', compact(
            'dataProvider',
            'registrations',
            'tm_start', 'tm_end',
            'email',
            'registrationConfirms',
            'isVIP',
            'isAdmin',
            'withChecks'
        ));
    }

    public function actionView($id)
    {
        $model = User::find()->where(compact('id'))->one();

        if (!$model) throw new NotFoundHttpException('Пользователь не найден');

        if (\Yii::$app->request->isPost) {
            $checks = \Yii::$app->request->post('checks');

            if ($checks !== null) {
                $model->checks += $checks;
                $model->save();
                \Yii::$app->session->setFlash('success', 'Проверки успешно начислены');
                return $this->refresh();
            }

            if ($model->load(Yii::$app->request->post())) {
                $model->save();
                return $this->refresh();
            }
        }

        $history = new ActiveDataProvider([
            'query' => SearchRequest::find()->where(['user_id' => $model->id]),
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
                'sortParam' => 'history-sort'
            ],
            'pagination' => [
                'pageParam' => 'history-page',
                'pageSize' => 25
            ]
        ]);

        $payments = new ActiveDataProvider([
            'query' => Payment::find()->where(['user_id' => $model->id]),
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
                'sortParam' => 'payments-sort'
            ],
            'pagination' => [
                'pageParam' => 'payments-page',
                'pageSize' => 25
            ]
        ]);

        $auth = new ActiveDataProvider([
            'query' => UserAuthLog::find()->where(['user_id' => $model->id]),
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
                'sortParam' => 'auth-sort'
            ],
            'pagination' => [
                'pageParam' => 'auth-page',
                'pageSize' => 25
            ]
        ]);

        return $this->render('view', compact('model', 'history', 'payments', 'auth'));
    }

    public function actionSetVip($id)
    {
        $user = User::find()->where(compact('id'))->one();
        if (!$user) throw new NotFoundHttpException('Пользователь не найден');
        $user->is_vip = !$user->is_vip;
        if (!$user->save()) {
            print_r($user->getErrors()); die();
        }
        return $this->redirect(Yii::$app->getRequest()->getReferrer());
    }

    public function actionSetAdmin($id)
    {
        $user = User::find()->where(compact('id'))->one();
        if (!$user) throw new NotFoundHttpException('Пользователь не найден');
        $user->is_admin = !$user->is_admin;
        $user->save();
        return $this->redirect(Yii::$app->getRequest()->getReferrer());
    }
}
