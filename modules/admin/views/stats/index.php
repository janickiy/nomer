<?php

/* @var $this \yii\web\View */
/* @var $sourcesStats array */
/* @var $start string */
/* @var $end string */

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\ResultCache;

$this->title = 'Статистика';

$sourcesDays = array_keys($sourcesStats);

krsort($sourcesDays);

$types = [];

foreach ($sourcesStats as $key => $value) {
    $types = array_merge($types, array_keys($value));
}

$types = array_filter(array_unique($types));

$sourcesToday = $sourcesDays[count($sourcesDays) - 1];
$sourcesYesterday = $sourcesDays[count($sourcesDays) - 2];

$latestRequestTime = function($date) {
    $timestamp = (new DateTime($date))->getTimestamp();

    switch (date('d', $timestamp)) {
        case date('d', time()):
            return '<span style="color: green;">' . date('(H:i)', $timestamp) . '</span>';
        case date('d', strtotime('-1 day')):
            return '<b><span style="color: red;">' . date('(вчера H:i)', $timestamp) . '</span></b>';
    }
};

$viberTime = function($date) {
    $hours = date('H');
    $time = null;

    if ($hours > 10 && $hours < 22) {
        $time = '-30 minutes';
    } else {
        $time = '-1 hour';
    }

    if (strtotime($time) > (new DateTime($date))->getTimestamp()) {
        return 'style="border-bottom: 2px red dashed;"';
    }

    return null;
};

?>

<table class="table table-striped table-bordered">
    <tbody>
        <tr>
            <th>Дата</th>
            <td>-</td>
            <?php foreach ($sourcesDays as $day): ?>
                <td><?= $day ?></td>
            <?php endforeach; ?>
        </tr>
        <?php foreach ($types as $type): ?>
            <tr>
                <th><?= ResultCache::getTypeName($type) ?></th>

                <?php foreach ($sourcesDays as $day): ?>
                    <?php if (isset($sourcesStats[$day][$type])): ?>
                        <?php if ($day == $sourcesToday && isset($sourcesStats[$sourcesYesterday][$type])): ?>
                            <?php if ($sourcesStats[$day][$type]['success']): ?>
                                <?php $data = (new \yii\db\Query())
                                    ->select(['phone', new \yii\db\Expression('MAX(tm) as tm')])
                                    ->from('cache')
                                    ->where(['type_id' => $type])
                                    ->andWhere(['and', ['<>', 'data', '[]'], ['<>', 'data', 'null']])
                                    ->groupBy('phone')
                                    ->orderBy(['tm' => SORT_DESC])
                                    ->one();
                                ?>
                                <td><span <?= $type == ResultCache::TYPE_VIBER ? $viberTime($data['tm']) : '' ?>><?= Html::a($data['phone'], '/' . preg_replace('/^7/', '8', $data['phone'])) ?> <?= $latestRequestTime($data['tm']) ?></span></td>
                            <?php else: ?>
                                <td>-</td>
                            <?php endif; ?>
                            <td style="cursor: pointer;" onclick="location.href='<?= Url::to(['stats/detailed', 'type' => $type, 'date' => $day]) ?>'" title="Всего запросов: <?= $sourcesStats[$day][$type]['all'] ?>, из них успешных: <?= $sourcesStats[$day][$type]['success'] ?>">
                                <?php $todayPercent = round($sourcesStats[$sourcesToday][$type]['success'] / $sourcesStats[$sourcesToday][$type]['all'] * 100, 2, PHP_ROUND_HALF_DOWN); ?>
                                <?php $yesterdayPercent = round($sourcesStats[$sourcesYesterday][$type]['success'] / $sourcesStats[$sourcesYesterday][$type]['all'] * 100, 2, PHP_ROUND_HALF_DOWN); ?>
                                <span style="color: <?= $todayPercent > ($yesterdayPercent / 2) ? 'green' : 'red' ?>"><?= $todayPercent ?>%</span>
                            </td>
                        <?php else: ?>
                            <td style="cursor: pointer;" onclick="location.href='<?= Url::to(['stats/detailed', 'type' => $type, 'date' => $day]) ?>'" title="Всего запросов: <?= $sourcesStats[$day][$type]['all'] ?>, из них успешных: <?= $sourcesStats[$day][$type]['success'] ?>">
                                <?= round($sourcesStats[$day][$type]['success'] / $sourcesStats[$day][$type]['all'] * 100, 2, PHP_ROUND_HALF_DOWN) ?>%
                            </td>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if ($day == $sourcesToday): ?>
                            <td>-</td>
                        <?php endif; ?>
                        <td>-</td>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
