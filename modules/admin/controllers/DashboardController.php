<?php

namespace app\modules\admin\controllers;

use app\models\ApplePayment;
use app\models\Payment;
use app\models\ResultCache;
use app\models\SearchRequest;
use app\models\User;
use app\models\UserSub;
use app\models\Wallet;
use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class DashboardController extends AdminController
{
    public function actionIndex()
    {
        $start = Yii::$app->request->get('tm_start', date('Y-m-d', strtotime('-7 days')));
        $end = Yii::$app->request->get('tm_end', date('Y-m-d'));

        $searchRequests = (new Query())
            ->select([
                'to_char(requests.tm, \'YYYY-MM-DD\') as date',
                'count(1) as requests',
            ])
            ->from('requests')
            ->where(['>=', 'requests.tm', $start . ' 00:00:00'])
            ->andWhere(['<=', 'requests.tm', $end . ' 23:59:59'])
            ->groupBy(['date'])
            ->orderBy(['date' => SORT_ASC])
            ->all();

        $users = (new Query())
            ->select(['to_char(tm_create, \'YYYY-MM-DD\') as date', 'count(1) as count'])
            ->from('users')
            ->where(['<=', 'tm_create', $end . ' 23:59:59'])
            ->andWhere(['>=', 'tm_create', $start . ' 00:00:00'])
            ->groupBy('date')
            ->orderBy(['date' => SORT_ASC])
            ->all();

        $applePayments = (new Query())
            ->select(['to_char(tm, \'YYYY-MM-DD\') as date', 'count(1) as count'])
            ->from('apple_payments')
            ->where(['<=', 'tm', $end . ' 23:59:59'])
            ->andWhere(['>=', 'tm', $start . ' 00:00:00'])
            ->andWhere(["refund" => 0])
            ->andWhere([">", "sum", 0])
            ->groupBy('date')
            ->orderBy(['date' => SORT_ASC])
            ->all();

        $requestsStats = [];

        $defaultStats = [
            'requests' => 0,
            'registrations' => 0,
            'applePayments' => 0
        ];


        foreach ($searchRequests as $searchRequest) {
            if (!isset($requestsStats[$searchRequest['date']])) $requestsStats[$searchRequest['date']] = $defaultStats;

            $requestsStats[$searchRequest['date']]['requests'] = $searchRequest['requests'];
        }

        foreach ($users as $user) {
            if (!isset($requestsStats[$user['date']])) $requestsStats[$user['date']] = $defaultStats;
            $requestsStats[$user['date']]['registrations'] = $user['count'];
        }

        foreach ($applePayments as $applePayment) {
            if (!isset($requestsStats[$applePayment['date']])) $requestsStats[$applePayment['date']] = $defaultStats;
            $requestsStats[$applePayment['date']]['applePayments'] = $applePayment['count'];
        }

        ksort($requestsStats);

        $payments = Payment::find()
            ->where(['>=', 'tm', date('Y-m-d 00:00:00', strtotime('-30 days'))])
            ->andWhere(["NOT IN", "type_id", [Payment::TYPE_TESTAPPLE, Payment::TYPE_ANDROID, Payment::TYPE_APPLE]])
            ->all();

        $today = (new yii\db\Query())
            ->select([
                new Expression('SUM(amount) as sum'),
                new Expression('COUNT(1) as bills'),
                new Expression('COUNT(DISTINCT user_id) as bills_users'),
                new Expression('(SELECT COUNT(1) FROM payments p WHERE 
                    extract(month from p.tm) = '.date("m").' 
                    AND extract(year from p.tm) = '.date("Y").' 
                    AND extract(day from p.tm) = '.date("d").' 
                    AND p.user_id IN (SELECT user_id FROM payments GROUP BY user_id HAVING COUNT(1) > 1)) as rebills'),
                new Expression('(SELECT COUNT(DISTINCT user_id) FROM payments p WHERE 
                    extract(month from p.tm) = '.date("m").' 
                    AND extract(year from p.tm) = '.date("Y").' 
                    AND extract(day from p.tm) = '.date("d").' 
                    AND p.user_id IN (SELECT user_id FROM payments GROUP BY user_id HAVING COUNT(1) > 1)) as rebills_users')
            ])
            ->from(Payment::tableName().' p')
            ->where(["=", new Expression("extract(month from tm)"), date("m")])
            ->andWhere(["=", new Expression("extract(year from tm)"), date("Y")])
            ->andWhere(["=", new Expression("extract(day from tm)"), date("d")])
            ->andWhere(["NOT IN", "type_id", [Payment::TYPE_TESTAPPLE, Payment::TYPE_APPLE]])
            ->one();

        $todayAndroid = (new yii\db\Query())
            ->select([
                new Expression('SUM(amount) as sum'),
                new Expression('COUNT(1) as bills'),
                new Expression('COUNT(DISTINCT user_id) as bills_users'),
                new Expression('(SELECT COUNT(1) FROM payments p WHERE 
                    extract(month from p.tm) = '.date("m").' 
                    AND extract(year from p.tm) = '.date("Y").' 
                    AND extract(day from p.tm) = '.date("d").' 
                    AND p.user_id IN (SELECT user_id FROM payments GROUP BY user_id HAVING COUNT(1) > 1)) as rebills'),
                new Expression('(SELECT COUNT(DISTINCT user_id) FROM payments p WHERE 
                    p.user_id IN (SELECT user_id FROM payments GROUP BY user_id HAVING COUNT(1) > 1)) as rebills_users')
            ])
            ->from(Payment::tableName().' p')
            ->where(["=", new Expression("extract(month from tm)"), date("m")])
            ->andWhere(["=", new Expression("extract(year from tm)"), date("Y")])
            ->andWhere(["=", new Expression("extract(day from tm)"), date("d")])
            ->andWhere(["type_id" => Payment::TYPE_ANDROID])
            ->one();

        $todayIos = (new yii\db\Query())
            ->select([
                new Expression('SUM(amount) as sum'),
                new Expression('COUNT(1) as bills')
            ])
            ->from(ApplePayment::tableName())
            ->where(["=", "tm", date("Y-m-d")])
            ->one();

        $yesterday = (new yii\db\Query())
            //->select([new Expression('SUM(amount) as sum'), new Expression('COUNT(1) as bills'), new Expression('COUNT(1) - (COUNT(DISTINCT user_id) + COUNT(case when user_id is NULL then 1 else null end)) as rebills')])
            ->select([
                new Expression('SUM(amount) as sum'),
                new Expression('COUNT(DISTINCT user_id) as bills_users'),
                new Expression('COUNT(1) as bills'),
                new Expression('(SELECT COUNT(1) FROM payments p WHERE 
                    extract(month from p.tm) = '.date("m", strtotime("-1 day")).' 
                    AND extract(year from p.tm) = '.date("Y", strtotime("-1 day")).' 
                    AND extract(day from p.tm) = '.date("d", strtotime("-1 day")).' 
                    AND p.user_id IN (SELECT user_id FROM payments GROUP BY user_id HAVING COUNT(1) > 1)) as rebills'),
                new Expression('(SELECT COUNT(DISTINCT user_id) FROM payments p WHERE 
                    extract(month from p.tm) = '.date("m", strtotime("-1 day")).' 
                    AND extract(year from p.tm) = '.date("Y", strtotime("-1 day")).' 
                    AND extract(day from p.tm) = '.date("d", strtotime("-1 day")).' 
                    AND p.user_id IN (SELECT user_id FROM payments GROUP BY user_id HAVING COUNT(1) > 1)) as rebills_users')
            ])
            ->from(Payment::tableName().' p')
            ->where(["=", new Expression("extract(month from tm)"), date("m", strtotime("-1 day"))])
            ->andWhere(["=", new Expression("extract(year from tm)"), date("Y", strtotime("-1 day"))])
            ->andWhere(["=", new Expression("extract(day from tm)"), date("d", strtotime("-1 day"))])
            ->andWhere(["NOT IN", "type_id", [Payment::TYPE_TESTAPPLE, Payment::TYPE_APPLE]])
            ->one();

        $yesterdayIos = (new yii\db\Query())
            ->select([
                new Expression('SUM(amount) as sum'),
                new Expression('COUNT(1) as bills')
            ])
            ->from(ApplePayment::tableName())
            ->where(["=", "tm", date("Y-m-d", strtotime("-1 day"))])
            ->one();

        $last30days = (new yii\db\Query())
            //->select([new Expression('SUM(amount) as sum'), new Expression('COUNT(1) as bills'), new Expression('COUNT(1) - (COUNT(DISTINCT user_id) + COUNT(case when user_id is NULL then 1 else null end)) as rebills')])
            ->select([
                new Expression('SUM(amount) as sum'),
                new Expression('COUNT(DISTINCT user_id) as bills_users'),
                new Expression('COUNT(1) as bills'),
                new Expression('(SELECT COUNT(1) FROM payments p WHERE 
                    tm >= \''.date("Y-m-d 00:00:00", strtotime("-30 days")).'\'
                    AND p.user_id IN (SELECT user_id FROM payments GROUP BY user_id HAVING COUNT(1) > 1)) as rebills'),
                new Expression('(SELECT COUNT(DISTINCT user_id) FROM payments p WHERE 
                    tm >= \''.date("Y-m-d 00:00:00", strtotime("-30 days")).'\'
                    AND p.user_id IN (SELECT user_id FROM payments GROUP BY user_id HAVING COUNT(1) > 1)) as rebills_users')
            ])
            ->from(Payment::tableName())
            ->where([">=", "tm", date("Y-m-d 00:00:00", strtotime("-30 days"))])
            ->andWhere(["<>", "type_id", Payment::TYPE_TESTAPPLE])
            ->andWhere(["IS NOT", "site_id", null])
            ->one();

        $last30Android = (new yii\db\Query())
            ->select([new Expression('SUM(amount) as sum'), new Expression('COUNT(1) as bills'), new Expression('COUNT(1) - (COUNT(DISTINCT user_id) + COUNT(case when user_id is NULL then 1 else null end)) as rebills')])
            ->from(Payment::tableName())
            ->where([">=", "tm", date("Y-m-d 00:00:00", strtotime("-30 days"))])
            ->andWhere(["type_id" => Payment::TYPE_ANDROID])
            ->one();

        $last30Ios = (new yii\db\Query())
            ->select([
                new Expression('SUM(amount) as sum'),
                new Expression('COUNT(1) as bills')
            ])
            ->from(ApplePayment::tableName())
            ->where([">=", "tm", date("Y-m-d", strtotime("-30 days"))])
            ->one();

        $yandexWalletsSum = Wallet::find()->where(["type_id" => Wallet::TYPE_YANDEX])->sum("balance");
        $qiwiWalletsSum = Wallet::find()->where(["type_id" => Wallet::TYPE_QIWI])->sum("balance");

        $start = Yii::$app->request->get('tm_start', date('Y-m-d', strtotime('-1 days')));
        $end = Yii::$app->request->get('tm_end', date('Y-m-d'));

        $results = (new Query())
            ->select(['to_char(requests.tm, \'YYYY-MM-DD\') as date', 'request_results.type_id', '
                CASE
                    WHEN (request_results.data = \'null\' OR request_results.data = \'[]\')
                    THEN false
                    ELSE true
                END as success', 'count(1)'])
            ->from('requests')
            ->innerJoin('request_results', ['requests.id' => new Expression('request_id')])
            ->where(['>', 'requests.tm', date('Y-m-d', strtotime('-1 days'))])
            ->groupBy(['date', 'request_results.type_id', 'success'])
            ->orderBy(['date' => SORT_ASC, 'request_results.type_id' => SORT_ASC, 'success' => SORT_ASC])
            ->all();

        $sourcesStats = [];

        foreach ($results as $result) {
            if (!isset($sourcesStats[$result['date']])) $sourcesStats[$result['date']] = [];
            if (!isset($sourcesStats[$result['date']][$result['type_id']])) {
                $sourcesStats[$result['date']][$result['type_id']] = [
                    'all' => 0,
                    'success' => 0
                ];
            }

            $sourcesStats[$result['date']][$result['type_id']]['all'] += $result['count'];
            if ($result['success']) $sourcesStats[$result['date']][$result['type_id']]['success'] += $result['count'];
        }

        //print_r($sourcesStats); die();

        $sourcesDays = array_keys($sourcesStats);

        krsort($sourcesDays);

        $types = [];

        foreach ($sourcesStats as $key => $value) {
            $types = array_merge($types, array_keys($value));
        }

        $types = array_filter(array_unique($types));

        $sourcesToday = $sourcesDays[count($sourcesDays) - 1];
        $sourcesYesterday = $sourcesDays[count($sourcesDays) - 2];

        $typesError = [];

        foreach($types as $type) {
            if(in_array($type, [ResultCache::TYPE_GETCONTACT, ResultCache::TYPE_VK_OPEN, ResultCache::TYPE_VK])) continue;
            $todayPercent = round(ArrayHelper::getValue($sourcesStats, [$sourcesToday, $type, "success"], 0) / ArrayHelper::getValue($sourcesStats, [$sourcesToday, $type, 'all'], 1) * 100, 2, PHP_ROUND_HALF_DOWN);
            $yesterdayPercent = round(
                ArrayHelper::getValue($sourcesStats, [$sourcesYesterday, $type, 'success'], 0)
                /
                ArrayHelper::getValue($sourcesStats, [$sourcesYesterday, $type, 'all'], 1) * 100, 2, PHP_ROUND_HALF_DOWN);
            if($yesterdayPercent > 0) {
                if($todayPercent < ($yesterdayPercent / 2)) {
                    $typesError[] = ResultCache::getTypeName($type);
                }
            }
        }

        $avitoStats = \Yii::$app->cache->get("avito");

        $notifyTokens = User::find()->where(["IS NOT", "token", null])->count(1);
        $notifyTokensWithoutSubs = User::find()->joinWith("subs")->where(["IS NOT", "token", null])->andWhere([UserSub::tableName().'.id' => null])->count(1);

        $todaySubs = UserSub::find()->where(new Expression(
            "extract(month from tm_purchase) = ".date("m")." 
                    AND extract(year from tm_purchase) = ".date("Y")." 
                    AND extract(day from tm_purchase) = ".date("d")
        ))->count(1);

        $yesterdaySubs = UserSub::find()->where(new Expression(
            "extract(month from tm_purchase) = ".date("m", strtotime("-1 day"))." 
                    AND extract(year from tm_purchase) = ".date("Y", strtotime("-1 day"))." 
                    AND extract(day from tm_purchase) = ".date("d", strtotime("-1 day"))
        ))->count(1);

        $last30daysSubs = UserSub::find()->where([">=", "tm_purchase", date("Y-m-d 00:00:00", strtotime("-30 days"))])->count(1);

        return $this->render('index', [
            'start'             => $start,
            'end'               => $end,
            'requestsStats'     => $requestsStats,
            'payments'          => $payments,
            'today'             => $today,
            'yesterday'         => $yesterday,
            'yesterdayIos'      => $yesterdayIos,
            'last30days'        => $last30days,
            'yandexWalletsSum'  => $yandexWalletsSum,
            'qiwiWalletsSum'    => $qiwiWalletsSum,
            'typesError'        => $typesError,
            'last30Android'     => $last30Android,
            'last30Ios'         => $last30Ios,
            'todayAndroid'      => $todayAndroid,
            'todayIos'          => $todayIos,
            'avitoStats'        => $avitoStats,
            'notifyTokens'      => $notifyTokens,
            'notifyTokensWithoutSubs'   => $notifyTokensWithoutSubs,
            'todaySubs'         => $todaySubs,
            'yesterdaySubs'     => $yesterdaySubs,
            'last30daysSubs'    => $last30daysSubs
        ]);
    }
}
