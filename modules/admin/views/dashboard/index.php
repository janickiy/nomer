<?php

/* @var $this \yii\web\View */
/* @var $start string */
/* @var $end string */
/* @var $requestsStats array */
/* @var $today array */
/* @var $todayIos array */
/* @var $yesterday array */
/* @var $last30days array */
/* @var $typesError array */
/* @var $notifyTokens int */
/* @var $notifyTokensWithoutSubs int */
/* @var $avitoStats */
/* @var $todaySubs */
/* @var $yesterdaySubs */
/* @var $last30daysSubs */
/* @var $yesterdayIos */

use app\models\SearchRequest;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\Url;
use yii\jui\DatePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Html;
use yii\web\View;
use app\models\Payment;

\app\modules\admin\assets\ChartsAsset::register($this);

$this->title = 'Dashboard';

$requestsDays = array_keys($requestsStats);

krsort($requestsDays);

$searchRequestsChart = [];
foreach ($requestsDays as $day):
    $count = 0;
    if (isset($requestsStats[$day])):
        $count = $requestsStats[$day]['requests'];
    endif;
    $searchRequestsChart[] = [
        "date" => $day,
        "value" => $count
    ];
endforeach;

krsort($searchRequestsChart);
$searchRequestsChart = array_values($searchRequestsChart);

$regsChart = [];
foreach ($requestsDays as $day):
    $count = 0;
    if (isset($requestsStats[$day])):
        $count = $requestsStats[$day]['registrations'];
    endif;
    $regsChart[] = [
        "date" => $day,
        "value" => $count
    ];
endforeach;

krsort($regsChart);
$regsChart = array_values($regsChart);

$appleChart = [];
foreach ($requestsDays as $day):
    $count = 0;
    if (isset($requestsStats[$day])):
        $count = $requestsStats[$day]['applePayments'];
    endif;
    $appleChart[] = [
        "date" => $day,
        "value" => $count
    ];
endforeach;

krsort($appleChart);
$appleChart = array_values($appleChart);

$siteTodayPayments = Payment::find()
    ->select(["site_id", new Expression('SUM(amount) as amount')])
    ->where([">=", "tm", date("Y-m-d 00:00:00")])
    ->andWhere(["<>", "type_id", [Payment::TYPE_TESTAPPLE, Payment::TYPE_ANDROID, Payment::TYPE_APPLE]])
    ->andWhere(["IS NOT", "site_id", null])
    ->groupBy(["site_id"])
    ->orderBy(["amount" => SORT_DESC])
    ->asArray()
    ->all();

$sitePayments = Payment::find()
    ->select(["site_id", new Expression('SUM(amount) as amount')])
    ->where([">=", "tm", date("Y-m-d 00:00:00", strtotime("-30 days"))])
    ->andWhere(["<>", "type_id", [Payment::TYPE_TESTAPPLE, Payment::TYPE_ANDROID, Payment::TYPE_APPLE]])
    ->andWhere(["IS NOT", "site_id", null])
    ->groupBy(["site_id"])
    ->orderBy(["amount" => SORT_DESC])
    ->asArray()
    ->all();

$sources = (new Query())
    ->select(['source_id', new Expression('COUNT(1)')])
    ->from('requests')
    ->join('LEFT JOIN', 'users', 'requests.user_id = users.id')
    ->where(['>=', 'tm', date("Y-m-d") . ' 00:00:00'])
    ->andWhere(['or', ['users.is_admin' => false], ['users.is_admin' => null]])
    ->groupBy('source_id')
    ->orderBy('source_id')
    ->all();

$sources = ArrayHelper::map($sources, "source_id", "count");

$sitesRequests = (new Query())
    ->select(['site_id', 'is_payed', 'source_id', new Expression('COUNT(1) as c')])
    ->from('requests')
    ->leftJoin('users', 'requests.user_id = users.id')
    ->where(['>=', 'tm', date("Y-m-d") . ' 00:00:00'])
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

$sites = \app\models\Site::find()->indexBy("id")->asArray()->all();

$last30daysSum = ArrayHelper::getValue($last30days, "sum") + ArrayHelper::getValue($last30Android, "sum") + ArrayHelper::getValue($last30Ios, "sum");

$antiparkonStats = \Yii::$app->cache->get("antiparkon");
?>

<?php if(!count($typesError)): ?>
    <div class="alert alert-success">Все поиски работают отлично</div>
<?php else: ?>
    <div class="alert alert-danger">У нас проблемы с: <?=join(", ", $typesError);?></div>
<?php endif; ?>

<?php if($avitoStats): ?>
    <div class="alert alert-<?=($avitoStats["api_limit"]["of"]-$avitoStats["api_limit"]["used"])>5000?"success":"danger";?>">Остаток поисков по Авито: <?=$avitoStats["api_limit"]["used"];?>/<?=$avitoStats["api_limit"]["of"];?>, оплачено до <?=\Yii::$app->formatter->asDatetime($avitoStats["api_time_limit"]);?></div>
<?php endif; ?>


<?php if($antiparkonStats): ?>
    <div class="alert alert-danger">Антипаркон: <?=$antiparkonStats;?></div>
<?php endif; ?>

<h3>Финансы</h3>

<table class="table table-striped table-bordered">
    <tr>
        <th>Сегодня</th>
        <td>Вчера</td>
        <td>За последние 30 дней</td>
    </tr>
    <tr>
        <th><?= Yii::$app->formatter->asCurrency(ArrayHelper::getValue($today, "sum") + ArrayHelper::getValue($todayIos, "sum"), 'RUB') ?>
            (Было
            <?=ArrayHelper::getValue($today, "bills")+ArrayHelper::getValue($todayIos, "bills");?>
            платежей от
            <?=ArrayHelper::getValue($today, "bills_users")+ArrayHelper::getValue($todayIos, "bills_users");?>
            пользователей, ребильных
            <?=ArrayHelper::getValue($today, "rebills")+ArrayHelper::getValue($todayIos, "rebills");?>
            платежей от
            <?=ArrayHelper::getValue($today, "rebills_users")+ArrayHelper::getValue($todayIos, "rebills_users");?> юзеров)</th>
        <th><?= Yii::$app->formatter->asCurrency(ArrayHelper::getValue($yesterday, "sum")+ArrayHelper::getValue($yesterdayIos, "sum"), 'RUB') ?>
            (Было
            <?=ArrayHelper::getValue($yesterday, "bills")+ArrayHelper::getValue($yesterdayIos, "bills");?>
            платежей от
            <?=ArrayHelper::getValue($yesterday, "bills_users")+ArrayHelper::getValue($yesterdayIos, "bills_users");?>
            пользователей, ребильных
            <?=ArrayHelper::getValue($yesterday, "rebills")+ArrayHelper::getValue($yesterdayIos, "rebills");?>
            платежей от
            <?=ArrayHelper::getValue($yesterday, "rebills_users")+ArrayHelper::getValue($yesterdayIos, "rebills_users");?>
            юзеров)</th>
        <th><?= Yii::$app->formatter->asCurrency($last30daysSum, 'RUB') ?>
            (Было
            <?=ArrayHelper::getValue($last30days, "bills");?>
            платежей от
            <?=ArrayHelper::getValue($last30days, "bills_users");?> пользователей, ребильных <?=ArrayHelper::getValue($last30days, "rebills");?> платежей от <?=ArrayHelper::getValue($last30days, "rebills_users");?> юзеров)</th>
    </tr>
    <tr>
        <th><?=\Yii::t('app', '{n,plural,=0{Подписок нет} =1{Одна подписка} one{# подписка} few{# подписки} many{# подписок} other{# подписки}}', ['n' => $todaySubs]);?></th>
        <th><?=\Yii::t('app', '{n,plural,=0{Подписок нет} =1{Одна подписка} one{# подписка} few{# подписки} many{# подписок} other{# подписки}}', ['n' => $yesterdaySubs]);?></th>
        <th><?=\Yii::t('app', '{n,plural,=0{Подписок нет} =1{Одна подписка} one{# подписка} few{# подписки} many{# подписок} other{# подписки}}', ['n' => $last30daysSubs]);?></th>
    </tr>
</table>

<table class="table table-striped table-bordered">
    <tr>
        <td>Общая сумма в кошельках - <b><?=Yii::$app->formatter->asCurrency($yandexWalletsSum + $qiwiWalletsSum, "RUB");?></b> из них в Яндекс - <b><?=Yii::$app->formatter->asCurrency($yandexWalletsSum, "RUB");?></b> и в киви - <b><?=Yii::$app->formatter->asCurrency($qiwiWalletsSum, "RUB");?></b></td>
    </tr>
</table>

<div class="row">
    <div class="col-md-6">
        <table class="table table-striped table-bordered">
            <tr>
                <th colspan="3">Кол-во поисков</th>
            </tr>
            <tr>
                <th>Сайт</th>
                <th>Web (платные)</th>
                <th>Mobile (платные)</th>
            </tr>
            <?php foreach ($sitesData as $siteID => $data): if(!$siteID) continue; ?>
                <tr>
                    <?php $name = ArrayHelper::getValue(ArrayHelper::getValue($sites, $siteID), 'name'); ?>
                    <?php $comment = ArrayHelper::getValue(ArrayHelper::getValue($sites, $siteID), 'comment'); ?>
                    <td><?= $name ? $name : 'Не указано' ?> <?= $comment ? '(' . $comment . ')' : '' ?></td>
                    <td><?= (ArrayHelper::getValue($data, [SearchRequest::SOURCE_WEB, 0], 0) + ArrayHelper::getValue($data, [SearchRequest::SOURCE_WEB, 1], 0)) ?> (<?= ArrayHelper::getValue($data, [SearchRequest::SOURCE_WEB, 1], 0) ?>)</td>
                    <td><?= (ArrayHelper::getValue($data, [SearchRequest::SOURCE_MOBILE, 0], 0) + ArrayHelper::getValue($data, [SearchRequest::SOURCE_MOBILE, 1], 0)) ?> (<?= ArrayHelper::getValue($data, [SearchRequest::SOURCE_MOBILE, 1], 0) ?>)</td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <th>Всего: </th>
                <td><?= ArrayHelper::getValue($sources, SearchRequest::SOURCE_WEB);?></td>
                <td><?= ArrayHelper::getValue($sources, SearchRequest::SOURCE_MOBILE);?></td>
            </tr>
        </table>
    </div>
    <div class="col-md-3">
        <table class="table table-bordered">
            <tr><th colspan="2">За сегодня</th></tr>
            <?php foreach ($siteTodayPayments as $item): ?>
                <tr>
                    <?php $name = ArrayHelper::getValue(ArrayHelper::getValue($sites, $item['site_id']), 'name'); ?>
                    <?php $comment = ArrayHelper::getValue(ArrayHelper::getValue($sites, $item['site_id']), 'comment'); ?>
                    <td><?= $name ? $name : 'Не указано' ?> <?= $comment ? '(' . $comment . ')' : '' ?></td>
                    <td><?= Yii::$app->formatter->asCurrency($item['amount'], 'RUB') ?></td>
                </tr>
            <?php endforeach; ?>
            <tr><td>Android</td><td><?=Yii::$app->formatter->asCurrency(ArrayHelper::getValue($todayAndroid, "sum", 0), 'RUB');?></td></tr>
            <tr><td>Apple</td><td><?=Yii::$app->formatter->asCurrency(ArrayHelper::getValue($todayIos, "sum", 0), 'RUB');?></td></tr>
        </table>
    </div>
    <div class="col-md-3">
        <table class="table table-bordered">
            <tr><th colspan="2">За 30 дней</th></tr>
            <?php foreach ($sitePayments as $item): ?>
                <tr>
                    <?php $name = ArrayHelper::getValue(ArrayHelper::getValue($sites, $item['site_id']), 'name'); ?>
                    <?php $comment = ArrayHelper::getValue(ArrayHelper::getValue($sites, $item['site_id']), 'comment'); ?>
                    <td><?= $name ? $name : 'Не указано' ?> <?= $comment ? '(' . $comment . ')' : '' ?></td>
                    <td><?= Yii::$app->formatter->asCurrency($item['amount'], 'RUB') ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td>Android</td>
                <td><?=Yii::$app->formatter->asCurrency($last30Android['sum'], 'RUB');?></td>
            </tr>
            <tr>
                <td>Apple</td>
                <td><?=Yii::$app->formatter->asCurrency($last30Ios['sum'], 'RUB');?></td>
            </tr>
        </table>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
<table class="table table-striped table-bordered">
    <tr>
        <td>Пользователей для уведомлений</td>
        <td><?=$notifyTokens;?></td>
    </tr>
    <tr>
        <td>Пользователей для уведомлений без подписки</td>
        <td><?=$notifyTokensWithoutSubs;?></td>
    </tr>
</table>
    </div>
</div>

<div class="portlet light bordered">
    <div class="portlet-title">
        <div class="caption">Фильтры</div>
    </div>
    <div class="portlet-body form">
        <?= Html::beginForm(['stats/index'], 'GET') ?>
        <div class="input-group input-medium">
            <span class="input-group-addon">С</span>
            <?= DatePicker::widget([
                'name'  => 'tm_start',
                'value'  => $start,
                'dateFormat' => 'yyyy-MM-dd',
                'options' => ['class' => 'form-control input-small']
            ]) ?>
            <span class="input-group-addon">по</span>
            <?= DatePicker::widget([
                'name'  => 'tm_end',
                'value'  => $end,
                'language' => 'ru',
                'dateFormat' => 'yyyy-MM-dd',
                'options' => ['class' => 'form-control input-small']
            ]) ?>
            <span class="input-group-addon"></span>
            <button type="submit" class="form-control btn btn-primary input-small">Показать</button>
        </div>

        <?= Html::endForm() ?>
    </div>
</div>

<table class="table table-striped table-bordered">
    <tr>
        <th>Дата</th>
        <?php foreach ($requestsDays as $day): ?>
            <td><?= $day ?></td>
        <?php endforeach; ?>
    </tr>
    <tr style="cursor: pointer;" onclick="renderSearchRequestsChart(); $(this).addClass('warning');">
        <th>Количество поисковых запросов</th>
        <?php foreach ($requestsDays as $day): ?>
            <td>
                <?php if (isset($requestsStats[$day])): ?>
                    <?= $requestsStats[$day]['requests'] ?>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr style="cursor: pointer;" onclick="renderRegsChart(); $(this).addClass('warning');">
        <th>Коичество регистраций</th>
        <?php foreach ($requestsDays as $day): ?>
            <td>
                <?php if (isset($requestsStats[$day])): ?>
                    <?= $requestsStats[$day]['registrations'] ?>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr style="cursor: pointer;" onclick="renderAppleChart(); $(this).addClass('warning');">
        <th>Кол-во покупок в Apple Store</th>
        <?php foreach ($requestsDays as $day): ?>
            <td>
                <?php if (isset($requestsStats[$day])): ?>
                    <?= $requestsStats[$day]['applePayments'] ?>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        <?php endforeach; ?>
    </tr>
</table>

<div id="chartdiv" style="height: 400px;"></div>

<?php if(false): ?><script language="JavaScript"><?php endif; ?>
<?php ob_start();?>
    function renderChart(data) {
        $('.warning').removeClass('warning');
        var chart = AmCharts.makeChart("chartdiv", {
            "type": "serial",
            "theme": "light",
            "marginRight": 40,
            "marginLeft": 40,
            "autoMarginOffset": 20,
            "mouseWheelZoomEnabled": false,
            "dataDateFormat": "YYYY-MM-DD",
            "valueAxes": [{
                "id": "v1",
                "axisAlpha": 0,
                "position": "left",
                "ignoreAxisWidth":true
            }],
            "balloon": {
                "borderThickness": 1,
                "shadowAlpha": 0
            },
            "graphs": [{
                "id": "g1",
                "balloon":{
                    "drop":true,
                    "adjustBorderColor":false,
                    "color":"#ffffff"
                },
                "bullet": "round",
                "bulletBorderAlpha": 1,
                "bulletColor": "#FFFFFF",
                "bulletSize": 2,
                "hideBulletsCount": 50,
                "lineThickness": 1,
                "title": "red line",
                "useLineColorForBulletBorder": true,
                "valueField": "value",
                "balloonText": "<span style='font-size:14px;'>[[value]]</span>"
            }],
            "chartCursor": {
                "pan": true,
                "valueLineEnabled": true,
                "valueLineBalloonEnabled": true,
                "cursorAlpha":1,
                "cursorColor":"#258cbb",
                "limitToGraph":"g1",
                "valueLineAlpha":0.2,
                "valueZoomable":true
            },
            "categoryField": "date",
            "categoryAxis": {
                "parseDates": true,
                "dashLength": 1,
                "minorGridEnabled": true
            },
            "dataProvider": data

        } );
    }
    function renderSearchRequestsChart() {
        renderChart(<?=Json::encode($searchRequestsChart);?>);
    }
    function renderRegsChart() {
        renderChart(<?=Json::encode($regsChart);?>);
    }
    function renderAppleChart() {
        renderChart(<?=Json::encode($appleChart);?>);
    }
<?php $js = ob_get_contents(); ob_end_clean(); $this->registerJs($js, View::POS_END); ?>
