<?php

namespace app\modules\admin\controllers;

use app\models\Payment;
use app\models\Site;
use Yii;
use app\models\SearchRequest;
use app\models\forms\AdminHistoryFilterForm;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class HistoryController extends AdminController
{
    public function actionIndex()
    {
        $model = new AdminHistoryFilterForm();

        $model->load(Yii::$app->getRequest()->get());

        $today = date('Y-m-d');

        if (!$model->from) {
            $model->from = $today;
        }

        if (!$model->to) {
            $model->to = $today;
        }

        $queries = (new Query())
            ->select(['
                CASE
                    WHEN (user_id IS NULL)
                    THEN false
                    ELSE true
                END as registred', 'count(1)'])
            ->from(SearchRequest::tableName())
            ->join('LEFT JOIN', 'users', 'requests.user_id = users.id')
            ->where(['>=', 'tm', $model->from . ' 00:00:00'])
            ->andWhere(['<=', 'tm', $model->to . ' 23:59:59'])
            ->andWhere(['or', ['users.is_admin' => false], ['users.is_admin' => null]])
            ->groupBy('registred')
            ->all();

        $users = Yii::$app->getDb()->createCommand('
            SELECT COUNT(DISTINCT user_id)
            FROM (SELECT DISTINCT phone, tm FROM requests WHERE user_id IS NULL) r1
            JOIN (SELECT DISTINCT phone, tm, user_id FROM requests WHERE user_id IS NOT NULL) r2
            ON r1.phone = r2.phone
            WHERE r1.tm >= :tm_start AND r1.tm <= :tm_end AND r1.tm < r2.tm;', [
                ':tm_start' => $model->from . ' 00:00:00',
                ':tm_end' => $model->to . ' 23:59:59'
        ])->queryAll();

        $phones = SearchRequest::find()
            ->select('requests.phone')
            ->joinWith(['user'])
            ->where(['>=', 'tm', $model->from . ' 00:00:00'])
            ->andWhere(['<=', 'tm', $model->to . ' 23:59:59'])
            ->andWhere(['or', ['users.is_admin' => false], ['users.is_admin' => null]])
            ->distinct()
            ->count();

        $sources = (new Query())
            ->select(['source_id', new Expression('COUNT(1)')])
            ->from('requests')
            ->join('LEFT JOIN', 'users', 'requests.user_id = users.id')
            ->where(['>=', 'tm', $model->from . ' 00:00:00'])
            ->andWhere(['<=', 'tm', $model->to . ' 23:59:59'])
            ->andWhere(['or', ['users.is_admin' => false], ['users.is_admin' => null]])
            ->groupBy('source_id')
            ->orderBy('source_id')
            ->all();

        $sitesRequests = (new Query())
            ->select(['site_id', 'is_payed', 'source_id', new Expression('COUNT(1) as c')])
            ->from('requests')
            ->leftJoin('users', 'requests.user_id = users.id')
            ->where(['>=', 'tm', $model->from . ' 00:00:00'])
            ->andWhere(['<=', 'tm', $model->to . ' 23:59:59'])
            ->andWhere(['or', ['users.is_admin' => false], ['users.is_admin' => null]])
            ->groupBy(['site_id', 'is_payed', 'source_id'])
            ->orderBy(['site_id' => SORT_ASC, 'is_payed' => SORT_ASC, 'source_id' => SORT_ASC])
            ->all();

        $sitesData = [];
        foreach($sitesRequests as $sr) {
            $is_payed = in_array($sr["is_payed"], [1, 2]);
            if(!isset($sitesData[$sr["site_id"]][$sr["source_id"]][$is_payed])) {
                $sitesData[$sr["site_id"]][$sr["source_id"]][$is_payed] = 0;
            }
            $sitesData[$sr["site_id"]][$sr["source_id"]][$is_payed] += $sr["c"];
        }

        $type = \Yii::$app->request->get("type", null);

        $query = SearchRequest::find()
            ->andWhere(['>=', 'tm', $model->from . ' 00:00:00'])
            ->andWhere(['<=', 'tm', $model->to . ' 23:59:59'])
            ->andWhere(['or', ['users.is_admin' => false], ['users.is_admin' => null]])
            ->joinWith(['user']);

        switch($type) {
            case 1:
                $query->andWhere(["is_payed" => 0, "is_has_name" => false, "is_has_photo" => false]);
                break;
            case 2:
                $query->andWhere(["is_payed" => 0, "is_has_name" => true, "is_has_photo" => true]);
                break;
            case 3:
                $query->andWhere(["is_payed" => 0, "is_has_name" => true, "is_has_photo" => false]);
                break;
            case 4:
                $query->andWhere(["is_payed" => [1, 2]]);
                break;
            case 5:
                $query->andWhere(["is_payed" => 2]);
                break;
            case 6:
                $query->andWhere(["is_payed" => 1]);
                break;
            case 7:
                $query->andWhere(["user_id" => null]);
                break;
            case 8:
                $query->andWhere(["is not", "user_id", null])->andWhere(["is_payed" => 0]);
                break;

        }

        $siteID = \Yii::$app->request->get("site_id", null);
        if($siteID) {
            $query->andWhere(['site_id' => $siteID]);
        }

        if ($model->user) {
            $query->andWhere(['user_id' => $model->user]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $sites = Site::find()->orderBy(["id" => SORT_ASC])->asArray()->indexBy("id")->all();

        $payments = Payment::find()->select(["site_id", new Expression("SUM(amount) as sum")])->where([">=", "tm", date("Y-m-d H:i:s", strtotime("-30 days"))])->groupBy("site_id")->indexBy("site_id")->asArray()->all();
        $searches = SearchRequest::find()->select(["site_id", new Expression("count(1) as count")])->where([">=", "tm", date("Y-m-d H:i:s", strtotime("-30 days"))])->groupBy("site_id")->indexBy("site_id")->asArray()->all();

        return $this->render('index', [
            'payments'      => $payments,
            'searches'      => $searches,
            'model'         => $model,
            'dataProvider'  => $dataProvider,
            'queries'       => $queries,
            'users'         => $users,
            'phones'        => $phones,
            'sources'       => $sources,
            'sites'         => $sites,
            'sitesData'     => $sitesData
        ]);
    }
}
