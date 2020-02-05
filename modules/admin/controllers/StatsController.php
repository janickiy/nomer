<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\Expression;
use yii\db\Query;
use app\models\SearchRequest;

class StatsController extends AdminController
{
    public function actionIndex()
    {
        $start = Yii::$app->request->get('tm_start', date('Y-m-d', strtotime('-7 days')));
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
            ->where(['>', 'requests.tm', date('Y-m-d', strtotime('-7 days'))])
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

        return $this->render('index', compact('start', 'end', 'sourcesStats'));
    }

    public function actionDetailed($type, $date, $filter = 'all')
    {
        $type = (int) $type;

        $query = (new Query())
            ->select(['requests.id', 'requests.phone', '
                CASE
                    WHEN (request_results.data = \'null\' OR request_results.data = \'[]\')
                    THEN false
                    ELSE true
                END as success', 'requests.tm'])
            ->from('requests')
            ->innerJoin('request_results', ['requests.id' => new Expression('request_id')])
            ->where(['request_results.type_id' => $type])
            ->andWhere(['>=', 'requests.tm', $date . ' 00:00:00'])
            ->andWhere(['<=', 'requests.tm', $date . ' 23:59:59']);

        switch ($filter) {
            case 'found': $query->andWhere(['and', ['<>', 'request_results.data', 'null'], ['<>', 'request_results.data', '[]']]); break;
            case 'not_found': $query->andWhere(['or', ['request_results.data' => 'null'], ['request_results.data' => '[]']]); break;
            default: $filter = 'all'; break;
        }

        $requests = $query->all();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $requests,
            'sort' => [
                'attributes' => ['id', 'success'],
                'defaultOrder' => ['id' => SORT_DESC]
            ]
        ]);

        return $this->render('detailed', compact('type', 'date', 'filter', 'dataProvider'));
    }
}
