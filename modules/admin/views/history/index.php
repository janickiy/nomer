<?php

/* @var $this \yii\web\View */
/* @var $model \app\models\forms\AdminHistoryFilterForm */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $queries array */
/* @var $users array */
/* @var $phones integer */
/* @var $sources array */
/* @var $sites array */
/* @var $requests array */
/* @var $sitesData array */
/* @var $searches array */
/* @var $payments array */

use app\models\Payment;
use yii\grid\GridView;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\jui\DatePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\SearchHelper;
use app\models\SearchRequest;
use app\models\RequestResult;
use app\models\ResultCache;

$this->title = 'История поиска';

$sources = ArrayHelper::map($sources, "source_id", "count");

$freeNothing = SearchRequest::find()
    ->where(["is_payed" => 0, "is_has_name" => false, "is_has_photo" => false])
    ->andWhere([">=", "tm", $model->from." 00:00:00"])
    ->andWhere(["<=", "tm", $model->to." 23:59:59"])
    ->andWhere(['or', ['users.is_admin' => false], ['users.is_admin' => null]])
    ->joinWith(['user'])
    ->count(1);
$freeAll = SearchRequest::find()
    ->where(["is_payed" => 0, "is_has_name" => true, "is_has_photo" => true])
    ->andWhere([">=", "tm", $model->from." 00:00:00"])
    ->andWhere(["<=", "tm", $model->to." 23:59:59"])
    ->andWhere(['or', ['users.is_admin' => false], ['users.is_admin' => null]])
    ->joinWith(['user'])
    ->count(1);
$freeOnlyName = SearchRequest::find()
    ->where(["is_payed" => 0, "is_has_name" => true, "is_has_photo" => false])
    ->andWhere([">=", "tm", $model->from." 00:00:00"])
    ->andWhere(["<=", "tm", $model->to." 23:59:59"])
    ->andWhere(['or', ['users.is_admin' => false], ['users.is_admin' => null]])
    ->joinWith(['user'])
    ->count(1);
$payed = SearchRequest::find()
    ->where(["is_payed" => 1])
    ->andWhere([">=", "tm", $model->from." 00:00:00"])
    ->andWhere(["<=", "tm", $model->to." 23:59:59"])
    ->andWhere(['or', ['users.is_admin' => false], ['users.is_admin' => null]])
    ->joinWith(['user'])
    ->count(1);
$payedNothing = SearchRequest::find()
    ->where(["is_payed" => 2])
    ->andWhere([">=", "tm", $model->from." 00:00:00"])
    ->andWhere(["<=", "tm", $model->to." 23:59:59"])
    ->andWhere(['or', ['users.is_admin' => false], ['users.is_admin' => null]])
    ->joinWith(['user'])
    ->count(1);
$anonymous = SearchRequest::find()
    ->where(["user_id" => null])
    ->andWhere([">=", "tm", $model->from." 00:00:00"])
    ->andWhere(["<=", "tm", $model->to." 23:59:59"])
    ->count(1);
$regged = SearchRequest::find()
    ->where(["is_payed" => 0])
    ->andWhere(["is not", "user_id", null])
    ->andWhere([">=", "tm", $model->from." 00:00:00"])
    ->andWhere(["<=", "tm", $model->to." 23:59:59"])
    ->count(1);

$androidSearches = SearchRequest::find()
    ->where(["source_id" => SearchRequest::SOURCE_ANDROID])
    ->andWhere([">=", "tm", $model->from." 00:00:00"])
    ->andWhere(["<=", "tm", $model->to." 23:59:59"])
    ->count(1);

$androidSearchesPayed = SearchRequest::find()
    ->where(["source_id" => SearchRequest::SOURCE_ANDROID])
    ->andWhere(["is_payed" => 1])
    ->andWhere([">=", "tm", $model->from." 00:00:00"])
    ->andWhere(["<=", "tm", $model->to." 23:59:59"])
    ->count(1);

$androidPayments = Payment::find()->where([">=", "tm", date("Y-m-d H:i:s", strtotime("-30 days"))])->andWhere(["type_id" => Payment::TYPE_ANDROID])->sum('amount');
$androidSearches30days = SearchRequest::find()->where([">=", "tm", date("Y-m-d H:i:s", strtotime("-30 days"))])->andWhere(["source_id" => SearchRequest::SOURCE_ANDROID])->count(1);

$appleSearches = SearchRequest::find()
    ->where(["source_id" => SearchRequest::SOURCE_IOS])
    ->andWhere([">=", "tm", $model->from." 00:00:00"])
    ->andWhere(["<=", "tm", $model->to." 23:59:59"])
    ->count(1);

$appleSearchesPayed = SearchRequest::find()
    ->where(["source_id" => SearchRequest::SOURCE_IOS])
    ->andWhere(["is_payed" => 1])
    ->andWhere([">=", "tm", $model->from." 00:00:00"])
    ->andWhere(["<=", "tm", $model->to." 23:59:59"])
    ->count(1);

$applePayments = Payment::find()->where([">=", "tm", date("Y-m-d H:i:s", strtotime("-30 days"))])->andWhere(["type_id" => Payment::TYPE_APPLE])->sum('amount');
$appleSearches30days = SearchRequest::find()->where([">=", "tm", date("Y-m-d H:i:s", strtotime("-30 days"))])->andWhere(["source_id" => SearchRequest::SOURCE_IOS])->count(1);
?>

<div class="portlet light bordered">
    <div class="portlet-title">
        <div class="caption">Фильтры</div>
    </div>
    <div class="portlet-body form">

<?php $form = ActiveForm::begin(['method' => 'GET', 'action' => '/admin/history']); ?>

<div class="input-group input-medium">
    <span class="input-group-addon">С</span>
    <?= $form->field($model, 'from', ['template' => '{input}', 'options' => ['class' => '']])->textInput()->widget(DatePicker::className(), [
        'language' => 'ru',
        'dateFormat' => 'yyyy-MM-dd',
        'options' => ['class' => 'form-control input-small']
    ]) ?>
    <span class="input-group-addon">по</span>
    <?= $form->field($model, 'to', ['template' => '{input}', 'options' => ['class' => '']])->widget(DatePicker::className(), [
        'language' => 'ru',
        'dateFormat' => 'yyyy-MM-dd',
        'options' => ['class' => 'form-control input-small']
    ]) ?>
    <?= $form->field($model, 'user', ['template' => '{input}', 'options' => ['class' => '']])->hiddenInput()->label(false) ?>
    <span class="input-group-addon"></span>
    <?= Html::submitButton('Показать', ['class' => 'form-control btn btn-primary input-small']) ?>
</div>

<?php ActiveForm::end(); ?>

    </div>
</div>

<hr>

<div class="row">
    <?php /*
    <div class="col-md-4">
        <table class="table table-striped table-bordered">
            <?php foreach ($queries as $query): ?>
                <tr>
                    <th><?= $query['registred'] ? 'Зарегистрированные' : 'Анонимные' ?></th>
                    <td><?= $query['count'] ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (count($users) > 0): ?>
                <tr>
                    <th>Сконвертировавшиеся пользователи</th>
                    <td><?= $users[0]['count'] ?></td>
                </tr>
            <?php endif; ?>
            <tr>
                <th>Количество уникальных номеров</th>
                <td><?= $phones ?></td>
            </tr>
        </table>
    </div>
    <div class="col-md-4">
        <table class="table table-striped table-bordered">
            <tr>
                <th><?=Html::a("бесплатные - анонимные", Url::current(["type" => 7]));?></th>
                <td><?=$anonymous;?></td>
            </tr>
            <tr>
                <th><?=Html::a("бесплатные - зареганные", Url::current(["type" => 8]));?></th>
                <td><?=$regged;?></td>
            </tr>
            <tr>
                <th><?=Html::a("бесплатные - ничего не найдено", Url::current(["type" => 1]));?></th>
                <td><?=$freeNothing;?></td>
            </tr>
            <tr>
                <th><?=Html::a("бесплатные - имя и фото", Url::current(["type" => 2]));?></th>
                <td><?=$freeAll;?></td>
            </tr>
            <tr>
                <th><?=Html::a("бесплатные - только имя", Url::current(["type" => 3]));?></th>
                <td><?=$freeOnlyName;?></td>
            </tr>
            <tr>
                <th><?=Html::a("платных запросов", Url::current(["type" => 4]));?></th>
                <td><?=($payedNothing + $payed);?></td>
            </tr>
            <tr>
                <th><?=Html::a("платные - ничего не найдено", Url::current(["type" => 5]));?></th>
                <td><?=$payedNothing;?></td>
            </tr>
            <tr>
                <th><?=Html::a("платные - найдено", Url::current(["type" => 6]));?></th>
                <td><?=$payed;?></td>
            </tr>
        </table>
    </div>
    */ ?>
    <div class="col-md-12">
        <table class="table table-striped table-bordered">
            <tr>
                <th colspan="3">Кол-во поисков</th>
            </tr>
            <tr>
                <th>Сайт</th>
                <th>Web (платные)</th>
                <th>Mobile (платные)</th>
                <th>Поиски (за 30 дн)</th>
                <th>Платежи (за 30 дн)</th>
            </tr>
            <?php foreach ($sitesData as $siteID => $data): if(!$siteID) continue; ?>
                <tr>
                    <th><?= Html::a($sites[$siteID]["name"], Url::current(["site_id" => $siteID])) ?> <?=$sites[$siteID]["comment"]?"(".$sites[$siteID]["comment"].")":"";?></th>
                    <td><?= (ArrayHelper::getValue($data, [SearchRequest::SOURCE_WEB, 0], 0) + ArrayHelper::getValue($data, [SearchRequest::SOURCE_WEB, 1], 0)) ?> (<?= ArrayHelper::getValue($data, [SearchRequest::SOURCE_WEB, 1], 0) ?>)</td>
                    <td><?= (ArrayHelper::getValue($data, [SearchRequest::SOURCE_MOBILE, 0], 0) + ArrayHelper::getValue($data, [SearchRequest::SOURCE_MOBILE, 1], 0)) ?> (<?= ArrayHelper::getValue($data, [SearchRequest::SOURCE_MOBILE, 1], 0) ?>)</td>
                    <td><?=ArrayHelper::getValue($searches, [$siteID, "count"]);?></td>
                    <td><?=\Yii::$app->formatter->asCurrency(ArrayHelper::getValue($payments, [$siteID, "sum"]), "RUB");?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <th>Android</th>
                <td colspan="2"><?=$androidSearches;?> (<?=$androidSearchesPayed;?>)</td>
                <td><?=$androidSearches30days;?></td>
                <td><?=\Yii::$app->formatter->asCurrency($androidPayments, 'RUB');?></td>
            </tr>
            <tr>
                <th>Apple</th>
                <td colspan="2"><?=$appleSearches;?> (<?=$appleSearchesPayed;?>)</td>
                <td><?=$appleSearches30days;?></td>
                <td><?=\Yii::$app->formatter->asCurrency($applePayments, 'RUB');?></td>
            </tr>
            <tr>
                <th>Всего: </th>
                <td><?= $sources[SearchRequest::SOURCE_WEB];?></td>
                <td><?= $sources[SearchRequest::SOURCE_MOBILE];?></td>
            </tr>
        </table>
    </div>
</div>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'tm',
        [
            'format' => 'raw',
            'attribute' => 'phone',
            'value' => function(SearchRequest $model) {
                $phone = preg_replace('/^7/', '8', $model->phone);
                return '<a href="/' . $phone . '">' . $phone . '</a>';
            }
        ],
        [
            'attribute' => 'user_id',
            'format' => 'raw',
            'value' => function(SearchRequest $model) {
                if (!$model->user) return 'Аноним';
                return Html::a(trim($model->user->email)?$model->user->email:$model->user->uuid, ['users/view', 'id' => $model->user_id]);
            }
        ],
        [
            'attribute' => 'ip',
            'value' => function(SearchRequest $model) {
                return join(', ', [$model->ip, SearchHelper::City($model->ip)]);
            }
        ],
        [
            'header' => 'Индекс поиска',
            'value' => function(SearchRequest $model) {
                if ($model->is_payed) {
                    return array_sum(array_map(function(RequestResult $result) {
                        return $result->index;
                    }, $model->results));
                }

                $finds = [];
                if ($model->is_has_name) $finds[] = 'Имя';
                if ($model->is_has_photo) $finds[] = 'Фото';

                return $finds ? join(', ', $finds) : 'Ничего не найдено';
            }
        ],
        [
            'header' => 'Стоимость',
            'value' => function(SearchRequest $model) {
                $type = '';

                switch ($model->is_payed) {
                    case 0: $type = 'Бесплатный (нет проверок)'; break;
                    case 1: $type = 'Платный'; break;
                    case 2: $type = 'Бесплатный (не нашли)'; break;
                }

                if (!$model->user_id) $type .= ', Аноним';

                return $type;
            }
        ],
        [
            'attribute' => 'source_id',
            'value' => function(SearchRequest $model) {
                return SearchRequest::getSourceText($model->source_id);
            }
        ],
        [
            'attribute' => 'site_id',
            'value' => function(SearchRequest $model) {
                if (!$model->site) return null;
                return $model->site->name;
            }
        ]
    ]
]) ?>
