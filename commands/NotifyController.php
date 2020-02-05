<?php

namespace app\commands;

use app\models\ResultCache;
use app\models\Telegram;
use yii\console\Controller;
use yii\db\Expression;
use yii\db\Query;

class NotifyController extends Controller
{
    protected function notify($peerID, $text)
    {
        return `/home/alexander/tg/bin/telegram-cli -k /home/alexander/tg/tg-server.pub -WR -e 'msg ${peerID} "${text}"'`;
    }

    public function actionQueue() {
        $jobCount = `/home/nomer.io/www/yii queue/info | grep waiting | grep -o '[0-9]*'`;
        if($jobCount > 15) {
            $this->notify('user#101209056', "В очереди ".$jobCount." запросов");
        }
    }

    public function actionIndex()
    {
        $todayResults = (new Query())
            ->select(['request_results.type_id', '
                CASE
                    WHEN (request_results.data = \'null\' OR request_results.data = \'[]\')
                    THEN false
                    ELSE true
                END as success
            ', 'count(1)'])
            ->from('requests')
            ->leftJoin('request_results', ['requests.id' => new Expression('request_id')])
            ->where(['>', 'requests.tm', date('Y-m-d H:i:s', strtotime('-24 hours'))])
            ->groupBy(['type_id', 'success'])
            ->orderBy(['type_id' => SORT_ASC, 'success' => SORT_ASC])
            ->all();

        $today = [];

        foreach ($todayResults as $result) {
            if ($result['type_id'] === null) continue;
            if (!isset($today[$result['type_id']])) $today[$result['type_id']] = ['all' => 0, 'success' => 0];
            if ($result['success']) $today[$result['type_id']]['success'] += $result['count'];
            $today[$result['type_id']]['all'] += $result['count'];
        }

        $yesterdayResults = (new Query())
            ->select(['request_results.type_id', '
                CASE
                    WHEN (request_results.data = \'null\' OR request_results.data = \'[]\')
                    THEN false
                    ELSE true
                END as success
            ', 'count(1)'])
            ->from('requests')
            ->leftJoin('request_results', ['requests.id' => new Expression('request_id')])
            ->where(['<=', 'requests.tm', date('Y-m-d H:i:s', strtotime('-24 hours'))])
            ->where(['>=', 'requests.tm', date('Y-m-d H:i:s', strtotime('-48 hours'))])
            ->groupBy(['type_id', 'success'])
            ->orderBy(['type_id' => SORT_ASC, 'success' => SORT_ASC])
            ->all();

        $yesterday = [];

        foreach ($yesterdayResults as $result) {
            if ($result['type_id'] === null) continue;
            if (!isset($yesterday[$result['type_id']])) $yesterday[$result['type_id']] = ['all' => 0, 'success' => 0];
            if ($result['success']) $yesterday[$result['type_id']]['success'] += $result['count'];
            $yesterday[$result['type_id']]['all'] += $result['count'];
        }

        $types = array_unique(array_merge(array_keys($today), array_keys($yesterday)));

        $text = '';

        foreach ($types as $type) {
            if (!isset($today[$type]) || !isset($yesterday[$type])) continue;

            $todayPercent = round($today[$type]['success'] / $today[$type]['all'] * 100, 2, PHP_ROUND_HALF_DOWN);
            $yesterdayPercent = round($yesterday[$type]['success'] / $yesterday[$type]['all'] * 100, 2, PHP_ROUND_HALF_DOWN);

            if ($todayPercent < ($yesterdayPercent / 2)) {
                $text .= ResultCache::getTypeName($type) . ': ' . $todayPercent . '%, ' . $yesterdayPercent . "%\n";
            }
        }

        $text = str_replace("\n", "\\n", trim($text));

        if ($text) echo $this->notify('user#101209056', $text);
    }

    public function actionViber()
    {
        $hours = date('H');

        $time = null;
        $timeText = null;

        if ($hours > 10 && $hours < 22) {
            $time = '-30 minutes';
            $timeText = 'последние 30 минут';
        } else {
            $time = '-1 hour';
            $timeText = 'последний час';
        }

        $results = ResultCache::find()
            ->where(['type_id' => ResultCache::TYPE_VIBER])
            ->andWhere(['>', 'tm', date('Y-m-d H:i:s', strtotime($time))])
            ->all();

        if (!$results) return;

        foreach ($results as $result) {
            if ($result['data'] != '[]' && $result['data'] != 'null') return;
        }

        echo $this->notify('user#101209056', 'За ' . $timeText . ' не было ни одного успешного поиска в Viber.');
    }

    public function actionTelegram()
    {
        $phones = [
            '79645552229',
            '79778979963',
            '79029111991'
        ];

        $instances = Telegram::find()->all();

        foreach ($instances as $instance) {
            $phone = $phones[array_rand($phones)];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://' . $instance->host . ':' . $instance->port . '/phone/' . $phone);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $instance->tm_last = new Expression('NOW()');

            switch ($code) {
                case 200: {
                    $instance->status = Telegram::STATUS_ACTIVE;
                    break;
                }
                case 404: {
                    $instance->status = Telegram::STATUS_INACTIVE;
                    break;
                }
                default: {
                    $instance->status = Telegram::STATUS_UNAVAILABLE;
                }
            }

            if ($code != 200) {
                echo $this->notify('user#101209056', 'Telegram ' . $instance->host . ' ' . $code . ' (номер ' . $phone . ').');
            }

            $instance->save();
        }
    }
}
