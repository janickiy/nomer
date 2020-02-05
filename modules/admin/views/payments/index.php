<?php

/* @var $this \yii\web\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $regions array */
/* @var $todaySubs int */

use app\models\ApplePayment;
use yii\db\Expression;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\SearchHelper;
use app\models\Payment;
use app\models\User;

$this->title = "Платежи";

$day = date("Y-m-d");
$days = [];

$today = (new yii\db\Query())
//    ->select([new Expression('SUM(amount) as sum'), new Expression('COUNT(1) as bills'), new Expression('COUNT(1) - (COUNT(DISTINCT user_id) + COUNT(case when user_id is NULL then 1 else null end)) as rebills')])
    ->select([new Expression('SUM(amount) as sum'), new Expression('COUNT(1) as bills'), new Expression('(SELECT COUNT(1) FROM payments p WHERE 
            extract(month from p.tm) = '.date("m").' 
            AND extract(year from p.tm) = '.date("Y").' 
            AND extract(day from p.tm) = '.date("d").' 
            AND p.user_id IN (SELECT user_id FROM payments GROUP BY user_id HAVING COUNT(1) > 1)) as rebills')])
    ->from(Payment::tableName().' p')
    ->where(["=", new Expression("extract(month from tm)"), date("m")])
    ->andWhere(["=", new Expression("extract(year from tm)"), date("Y")])
    ->andWhere(["=", new Expression("extract(day from tm)"), date("d")])
    ->andWhere(["<>", "type_id", Payment::TYPE_TESTAPPLE])
    ->one();

$last30days = Payment::find()
    ->select(new Expression('SUM(amount)'))
    ->where([">=", "tm", date("Y-m-d 00:00:00", strtotime("-30 days"))])
    ->andWhere(["NOT IN", "type_id", [Payment::TYPE_TESTAPPLE, Payment::TYPE_APPLE]])
    ->scalar();

$last30daysDetails = Payment::find()
    ->select(["type_id", new Expression('SUM(amount) as amount')])
    ->where([">=", "tm", date("Y-m-d 00:00:00", strtotime("-30 days"))])
    ->andWhere(["NOT IN", "type_id", [Payment::TYPE_TESTAPPLE, Payment::TYPE_APPLE]])
    ->groupBy(["type_id"])
    ->asArray()
    ->all();

$currentMonth = Payment::find()
    ->select(new Expression('SUM(amount)'))
    ->where(["=", new Expression("extract(month from tm)"), date("m")])
    ->andWhere(["=", new Expression("extract(year from tm)"), date("Y")])
    ->andWhere(["<>", "type_id", Payment::TYPE_TESTAPPLE])
    ->scalar();

/*
$currentMonthDetails = Payment::find()
    ->select(["type_id", new Expression('SUM(amount) as amount')])
    ->where(["=", new Expression("extract(month from tm)"), date("m")])
    ->andWhere(["=", new Expression("extract(year from tm)"), date("Y")])
    ->andWhere(["<>", "type_id", Payment::TYPE_TESTAPPLE])
    ->groupBy(["type_id"])
    ->asArray()
    ->all();
*/

$preventMonth = Payment::find()
    ->select(new Expression('SUM(amount)'))
    ->where(["=", new Expression("extract(month from tm)"), date("m", strtotime("-1 month"))])
    ->andWhere(["=", new Expression("extract(year from tm)"), date("Y", strtotime("-1 month"))])
    ->andWhere(["<>", "type_id", Payment::TYPE_TESTAPPLE])
    ->scalar();

/*
$preventMonthDetails = Payment::find()
    ->select(["type_id", new Expression('SUM(amount) as amount')])
    ->where(["=", new Expression("extract(month from tm)"), date("m", strtotime("-1 month"))])
    ->andWhere(["=", new Expression("extract(year from tm)"), date("Y", strtotime("-1 month"))])
    ->andWhere(["<>", "type_id", Payment::TYPE_TESTAPPLE])
    ->groupBy(["type_id"])
    ->asArray()
    ->all();
*/

$paymentsData = [
    //"all" => [0 => 0, 1 => 0, 2 => 0],
    Payment::TYPE_YANDEX => [0 => 0],
    Payment::TYPE_YANDEX_WALLET => [0 => 0],
    Payment::TYPE_QIWI => [0 => 0],
    Payment::TYPE_QIWI_TERMINAL => [0 => 0],
    Payment::TYPE_WEBMONEY => [0 => 0]
];

foreach($last30daysDetails as $r) {
    $paymentsData[$r["type_id"]][0] = $r["amount"];
}

$iosPayments = (new yii\db\Query())
    ->select([
        new Expression('SUM(amount) as amount'),
    ])
    ->from(ApplePayment::tableName())
    ->where([">=", "tm", date("Y-m-d", strtotime("-30 days"))])
    ->scalar();

$paymentsData[9][0] = $iosPayments;
/*
foreach($currentMonthDetails as $r) {
    $paymentsData[$r["type_id"]][1] = $r["amount"];
}
foreach($preventMonthDetails as $r) {
    $paymentsData[$r["type_id"]][2] = $r["amount"];
}
*/

$siteTodayPayments = Payment::find()
    ->select(["site_id", new Expression('SUM(amount) as amount')])
    ->where([">=", "tm", date("Y-m-d 00:00:00")])
    ->andWhere(["<>", "type_id", Payment::TYPE_TESTAPPLE])
    ->andWhere(["IS NOT", "site_id", null])
    ->groupBy(["site_id"])
    ->orderBy(["amount" => SORT_DESC])
    ->asArray()
    ->all();

$siteTodayMobilePayments = Payment::find()
    ->select(["type_id", new Expression('SUM(amount) as amount')])
    ->where([">=", "tm", date("Y-m-d 00:00:00")])
    ->andWhere(["type_id" => [Payment::TYPE_ANDROID]])
    ->andWhere(["site_id" => null])
    ->groupBy(["type_id"])
    ->orderBy(["amount" => SORT_DESC])
    ->asArray()
    ->all();

$sitePayments = Payment::find()
    ->select(["site_id", new Expression('SUM(amount) as amount')])
    ->where([">=", "tm", date("Y-m-d 00:00:00", strtotime("-30 days"))])
    ->andWhere(["<>", "type_id", Payment::TYPE_TESTAPPLE])
    ->andWhere(["IS NOT", "site_id", null])
    ->groupBy(["site_id"])
    ->orderBy(["amount" => SORT_DESC])
    ->asArray()
    ->all();

$mobilePayments = Payment::find()
    ->select(["type_id", new Expression('SUM(amount) as amount')])
    ->where([">=", "tm", date("Y-m-d 00:00:00", strtotime("-30 days"))])
    ->andWhere(["type_id" => [Payment::TYPE_ANDROID]])
    ->andWhere(["site_id" => null])
    ->groupBy(["type_id"])
    ->orderBy(["amount" => SORT_DESC])
    ->asArray()
    ->all();



$siteAllPayments = Payment::find()
    ->select(["site_id", new Expression('SUM(amount) as amount')])
    ->andWhere(["<>", "type_id", Payment::TYPE_TESTAPPLE])
    ->andWhere(["IS NOT", "site_id", null])
    ->groupBy(["site_id"])
    ->orderBy(["amount" => SORT_DESC])
    ->asArray()
    ->all();

$mobileAllPayments = Payment::find()
    ->select(["type_id", new Expression('SUM(amount) as amount')])
    ->andWhere(["type_id" => [Payment::TYPE_ANDROID]])
    ->andWhere(["site_id" => null])
    ->groupBy(["type_id"])
    ->orderBy(["amount" => SORT_DESC])
    ->asArray()
    ->all();

$iosAllPayment = (new yii\db\Query())
    ->select([
        new Expression('SUM(amount) as amount'),
    ])
    ->from(ApplePayment::tableName())
    ->scalar();

$sites = \app\models\Site::find()->indexBy("id")->asArray()->all();

$siteTodayData = $site30daysData = $siteAllData = [];
foreach ($siteTodayPayments as $s) {
    //$sTitle =
    $name = ArrayHelper::getValue(ArrayHelper::getValue($sites, $s['site_id']), 'name');
    $comment = ArrayHelper::getValue(ArrayHelper::getValue($sites, $s['site_id']), 'comment');
    $siteId = $name.($comment?'('.$comment.')':'');
    $siteTodayData[$siteId] = ArrayHelper::getValue($s, "amount");
}
foreach ($siteTodayMobilePayments as $s) {
    $siteId = Payment::getTypeName(ArrayHelper::getValue($s, "type_id"));
    $siteTodayData[$siteId] = ArrayHelper::getValue($s, "amount");
}


foreach ($sitePayments as $s) {
    //$sTitle =
    $name = ArrayHelper::getValue(ArrayHelper::getValue($sites, $s['site_id']), 'name');
    $comment = ArrayHelper::getValue(ArrayHelper::getValue($sites, $s['site_id']), 'comment');
    $siteId = $name.($comment?'('.$comment.')':'');
    $site30daysData[$siteId] = ArrayHelper::getValue($s, "amount");
}
foreach ($mobilePayments as $s) {
    $siteId = Payment::getTypeName(ArrayHelper::getValue($s, "type_id"));
    $site30daysData[$siteId] = ArrayHelper::getValue($s, "amount");
}

$site30daysData["Apple"] = $iosPayments;

foreach ($siteAllPayments as $s) {
    //$sTitle =
    $name = ArrayHelper::getValue(ArrayHelper::getValue($sites, $s['site_id']), 'name');
    $comment = ArrayHelper::getValue(ArrayHelper::getValue($sites, $s['site_id']), 'comment');
    $siteId = $name.($comment?'('.$comment.')':'');
    $siteAllData[$siteId] = ArrayHelper::getValue($s, "amount");
}
foreach ($mobileAllPayments as $s) {
    $siteId = Payment::getTypeName(ArrayHelper::getValue($s, "type_id"));
    $siteAllData[$siteId] = ArrayHelper::getValue($s, "amount");
}

$siteAllData["Apple"] = $iosAllPayment;

arsort($siteTodayData);
arsort($site30daysData);
arsort($siteAllData);


?>
<div class="row">
    <div class="col-md-3">
        <table class="table table-bordered">
            <tr><td colspan="4"><b>За сегодня: <?= Yii::$app->formatter->asCurrency(ArrayHelper::getValue($today, 'sum', 0), 'RUB') ?> (Всего платежей - <?= ArrayHelper::getValue($today, 'bills', 0) ?>, ребилов - <?= ArrayHelper::getValue($today, 'rebills', 0) ?>)</b><br>
                    <?=\Yii::t('app', '{n,plural,=0{Подписок нет} =1{Одна подписка} one{# подписка} few{# подписки} many{# подписок} other{# подписки}}', ['n' => $todaySubs]);?>
                </td></tr>
            <tr>
                <td></td>
                <td><b>За последние 30 дней</b></td>
            </tr>
            <?php foreach ($paymentsData as $typeID => $items): ?>
                <tr>
                    <td><?=Payment::getTypeName($typeID);?></td>
                    <?php foreach($items as $item): ?>
                        <td><?=\Yii::$app->formatter->asCurrency($item, "RUB");?></td>
                    <?php endforeach;?>
                </tr>
            <?php endforeach; ?>
            <tr>
                <th>Всего:</th>
                <th><?=\Yii::$app->formatter->asCurrency($last30days+$iosPayments, "RUB");?></th>
            </tr>
        </table>
    </div>
    <div class="col-md-3">
        <table class="table table-bordered">
            <tr><th colspan="2">За сегодня</th></tr>
            <?php foreach ($siteTodayData as $name => $amount): ?>
                <tr>
                    <td><?=$name;?></td>
                    <td><?= Yii::$app->formatter->asCurrency($amount, 'RUB') ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <div class="col-md-3">
        <table class="table table-bordered">
            <tr><th colspan="2">За 30 дней</th></tr>
            <?php foreach ($site30daysData as $name => $amount): ?>
                <tr>
                    <td><?=$name;?></td>
                    <td><?= Yii::$app->formatter->asCurrency($amount, 'RUB') ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <div class="col-md-3">
        <table class="table table-bordered">
            <tr><th colspan="2">За всё время</th></tr>
            <?php foreach ($siteAllData as $name => $amount): ?>
                <tr>
                    <td><?=$name;?></td>
                    <td><?= Yii::$app->formatter->asCurrency($amount, 'RUB') ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
<?php
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'rowOptions' => function (Payment $model) {
        $payments = Payment::find()->where(['user_id' => $model->user_id])->andWhere(['<', 'id', $model->id])->count();
        if ($payments) return ['class' => 'success'];
        return [];
    },
    'beforeRow' => function ($model, $key, $index, $grid) use (&$day){
        $cday = date("Y-m-d", strtotime($model->tm));
        if($cday != $day && $index > 0) {
            $sum = Payment::find()->select(new Expression('SUM(amount)'))->where([">=", "tm", $day." 00:00:00"])->andWhere(["<=", "tm", $day." 23:59:59"])->scalar();
            return "<tr><td colspan='5'>За <b>".\Yii::$app->formatter->asDate($day, "d MMMM")."</b> заработано <b>".\Yii::$app->formatter->asCurrency($sum, "RUB")."</b></td></tr>";
        }
        $day = date("Y-m-d", strtotime($model->tm));
    },
    'afterRow' => function ($model, $key, $index, $grid) use (&$day, $dataProvider) {
        $day = date("Y-m-d", strtotime($model->tm));
        if(($index+1) == $dataProvider->totalCount) {
            $sum = Payment::find()->select(new Expression('SUM(amount)'))->where([">=", "tm", $day." 00:00:00"])->andWhere(["<=", "tm", $day." 23:59:59"])->scalar();
            return "<tr><td colspan='5'>За <b>".\Yii::$app->formatter->asDate($day, "d MMMM")."</b> заработано <b>".\Yii::$app->formatter->asCurrency($sum, "RUB")."</b></td></tr>";
        }
    },
    'columns' => [
        'id',
        [
            'attribute' => 'site_id',
            'content' => function(Payment $model) {
                if($model->type_id == Payment::TYPE_ANDROID) {
                    return "Play Market";
                }
                if($model->type_id == Payment::TYPE_APPLE) {
                    return "Apple Store";
                }
                return ArrayHelper::getValue($model, "site.name", "-");
            }
        ],
        [
            'attribute' => 'tm',
            'content' => function(Payment $model) {
                return Yii::$app->formatter->asDatetime(strtotime($model->tm) - 3600, 'dd.MM.yyyy HH:mm');
            }
        ],
        [
            'attribute' => 'user_id',
            'content' => function(Payment $model) {
                $user = ArrayHelper::getValue($model, 'user', null);

                $city = '';
                if (!$user || $user->ip == '') $city = '';
                //else $city = SearchHelper::City($user->ip);

                $paymentsCount = count(ArrayHelper::getValue($model, ['user', 'payments'], []));

                if ($paymentsCount > 1) {
                    $paymentsSum = array_sum(ArrayHelper::getColumn($model->user->payments, 'amount'));
                    $paymentsSum = Yii::$app->formatter->asCurrency($paymentsSum, 'RUB');
                    $payments = '(Всего ' . $paymentsSum .', ' . Yii::t('app', '{n, plural, one{# платёж} few{# платежа} many{# платежей} other{# платежа}}', ['n' => $paymentsCount]) . ')';
                } else {
                    $payments = '';
                }

                $username = null;
                if ($user) {
                    $username = 'Пользователь #'.$user->id;
                    if(trim($user->email) != "") {
                        $username = $user->email;
                    }
                }


                return $user ? join(', ', array_filter([Html::a($username, Url::toRoute(['users/view', 'id' => $user->id])), $city, $payments])) : "Пользователь не известен";
            }
        ],
        [
            'header' => 'Сумма',
            'content' => function(Payment $model) {
                return Yii::$app->formatter->asCurrency($model->amount, 'RUB');
            }
        ],
        [
            'header' => 'Тип',
            'content' => function(Payment $model) {
                return Payment::getTypeName($model->type_id);
            }
        ]
    ]
]);
