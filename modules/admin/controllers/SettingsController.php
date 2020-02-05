<?php

namespace app\modules\admin\controllers;

use app\models\UrlFilter;
use Yii;
use yii\data\ActiveDataProvider;
use app\models\Settings;
use app\models\User;
use yii\data\SqlDataProvider;
use yii\db\Query;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

class SettingsController extends AdminController
{
    public function actionIndex($tab = 'index')
    {
        switch ($tab) {
            case 'index': {
                if (Yii::$app->getRequest()->getIsPost()) {
                    foreach (Yii::$app->getRequest()->post() as $key => $value) {
                        Settings::set($key, $value);
                    }

                    return $this->redirect(['settings/index', 'tab' => 'index']);
                }
            }
            case 'bans': {
                $dataProvider = new ActiveDataProvider([
                    'query' => User::find()->where(['status' => 0]),
                    'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
                ]);

                return $this->render('index', compact('tab', 'dataProvider'));
            }
            case 'domains': {
                $model = new UrlFilter();
                $dataProvider = new ActiveDataProvider([
                    'query' => UrlFilter::find(),
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

                return $this->render('index', compact('tab', 'model', 'dataProvider'));
            }
            case 'fingerprints': {
                $dataProvider = new SqlDataProvider([
                    'sql' => \Yii::$app->db->createCommand("SELECT hash, array_agg(user_id) as user_ids, array_agg(ip) as ips FROM \"user_fingerprints\" GROUP BY \"hash\" HAVING COUNT(1) > 1")->rawSql,
                    'sort' => false
                ]);

                return $this->render('index', compact('tab', 'dataProvider'));
            }
            case 'blocked-phones': {
                $results = (new Query())
                    ->select(['to_char(tm, \'YYYY-MM-DD\') as date', 'status',  'count(1) as count'])
                    ->from('block')
                    ->groupBy(['date', 'status'])
                    ->orderBy(['date' => SORT_DESC, 'status' => SORT_ASC])
                    ->all();

                $phones = [];

                foreach ($results as $result) {
                    if (!isset($phones[$result['date']])) $phones[$result['date']] = [
                        'all' => 0,
                        'unconfirmed' => 0,
                        'confirmed' => 0,
                        'vip' => 0
                    ];

                    switch ($result['status']) {
                        case 0:
                            $phones[$result['date']]['unconfirmed'] = $result['count'];
                            break;
                        case 1:
                            $phones[$result['date']]['confirmed'] = $result['count'];
                            break;
                        case 2:
                            $phones[$result['date']]['vip'] = $result['count'];
                            break;
                    }

                    $phones[$result['date']]['all'] += $result['count'];
                }

                return $this->render('index', compact('tab', 'phones'));
            }
            default: throw new NotFoundHttpException('Страница не найдена.');
        }
    }

    public function actionDeleteDomain($id)
    {
        if ($domain = UrlFilter::findOne($id)) $domain->delete();
        if (!$domain) throw new NotFoundHttpException('Домен не найден.');
        $referrer = Yii::$app->getRequest()->getReferrer();
        $url = $referrer ? $referrer : Url::to(['settings/index', 'tab' => 'domains']);
        return $this->redirect($url);
    }
}
