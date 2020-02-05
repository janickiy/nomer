<?php

/* @var $this \yii\web\View */
/* @var $model \app\models\User */
/* @var $history \yii\data\ActiveDataProvider */
/* @var $payments \yii\data\ActiveDataProvider */
/* @var $auth \yii\data\ActiveDataProvider */

use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Html;
use app\components\SearchHelper;
use app\models\Payment;
use app\models\ResultCache;
use app\models\RequestResult;
use app\models\SearchRequest;

$this->title = 'Пользователь #' . $model->id;

$index = 0;

foreach($model->requests as $r) {
    $index += array_sum(ArrayHelper::getColumn($r->results, 'index'));
}

$index = round(count($model->requests) ? ($index / count($model->requests)) : 0, 2);


$indexToday = 0;

$requests = array_filter($model->requests, function($r) {
    return date('Y-m-d', strtotime($r->tm)) == date('Y-m-d');
});

foreach($requests as $r) {
    $indexToday += array_sum(ArrayHelper::getColumn($r->results, 'index'));
}

$indexToday = round(count($requests) ? ($indexToday / count($requests)) : 0, 2);

?>

<div class="row">
    <div class="col-md-12">
        <div class="portlet light ">
            <div class="portlet-title tabbable-line">
                <div class="caption caption-md">
                    <i class="icon-globe theme-font hide"></i>
                    <span class="caption-subject font-blue-madison bold uppercase">Пользователь #<?= $model->id ?></span>
                </div>
            </div>
            <div class="portlet-body">
                <p>E-mail: <?= $model->email ?>, Пароль: <?= $model->password ?>, Дата регистрации: <?= $model->tm_create ?></p>
                <p><b><?= Yii::t('app', 'Остаток: {n, plural, =0{проверок нет} one{# проверка} few{# проверки} many{# проверок} other{# проверок}}', ['n' => $model->checks]) ?>, Общая сумма платежей: <?= array_sum(array_map(function(Payment $payment) { return $payment->sum; }, $model->payments)) ?> рублей</b></p>
                <p>Поисков всего: <?= count($model->requests) ?>, Средний индекс поиска: <?= $index ?>%, Поисков за сегодня: <?= count(array_filter($model->requests, function($r) {
                        return date('Y-m-d', strtotime($r->tm)) == date('Y-m-d');
                    })) ?>, Средний индекс поиска за сегодня: <?= $indexToday ?>%</p>

                <?php if ($success = \Yii::$app->session->getFlash('success')): ?>
                    <div class="alert alert-success"><strong><?= $success ?></strong></div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-3" style="margin-top: 25px;">
                        <?= Html::beginForm('', 'POST', ['class' => 'form-inline']) ?>
                        <div class="form-group">
                            <input name="checks" value="10" class="form-control input-small">
                            <button type="submit" class="btn btn-primary" style="display: inline-block">Начислить</button>
                        </div>
                        <?= Html::endForm() ?>

                        <?= $model->is_vip ? Html::a('Убрать VIP', ['set-vip', 'id' => $model->id], ['class' => 'btn btn-danger', 'style' => 'margin-top: 15px;']) : Html::a('Поставить VIP', ['set-vip', 'id' => $model->id], ['class' => 'btn btn-success', 'style' => 'margin-top: 15px;']) ?>

                        <?= $model->is_admin ? Html::a('Убрать админа', ['set-admin', 'id' => $model->id], ['class' => 'btn btn-danger', 'style' => 'margin-top: 15px;']) : Html::a('Поставить админа', ['set-admin', 'id' => $model->id], ['class' => 'btn btn-success', 'style' => 'margin-top: 15px;']) ?>
                    </div>

                    <div class="col-md-4">
                        <?php $form = \yii\widgets\ActiveForm::begin(['method' => 'POST']); ?>
                        <?= $form->field($model, 'comment')->textInput() ?>
                        <p><?= Html::submitButton('Сохранить комментарий', ['class' => 'btn btn-primary']) ?></p>
                        <?php \yii\widgets\ActiveForm::end(); ?>
                    </div>
                </div>

                <hr>

                <h2>Платежи</h2>

                <?= GridView::widget([
                    'dataProvider' => $payments,
                    'columns' => [
                        'id',
                        'tm',
                        'sum',
                        'amount',
                        [
                            'header' => 'Тип',
                            'attribute' => 'type_id',
                            'content' => function(Payment $model) {
                                switch ($model->type_id) {
                                    case Payment::TYPE_QIWI: return "Qiwi Wallet";
                                    case Payment::TYPE_YANDEX: return "Яндекс.Деньги Card";
                                    case Payment::TYPE_WEBMONEY: return "WebMoney ";
                                    case Payment::TYPE_QIWI_TERMINAL: return "Qiwi терминал";
                                    case Payment::TYPE_YANDEX_WALLET: return "Яндекс.Деньги Wallet";
                                }
                                return "";
                            }
                        ],
                        'operation_id',
                        'operation_label'
                    ]
                ]) ?>

                <hr>

                <h2>История поиска</h2>

                <?= GridView::widget([
                    'dataProvider' => $history,
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

                                $names = $photos = [];
                                $caches = RequestResult::find()
                                    ->where(['request_id' => $model->id])
                                    ->andWhere(['not in', 'type_id', ResultCache::inactiveTypes()])->all();

                                foreach ($caches as $c) {
                                    $data = Json::decode($c->data);
                                    if (!$data) continue;
                                    $names = ArrayHelper::merge($names, ArrayHelper::getColumn($data, 'name'));
                                    $photos = ArrayHelper::merge($photos, ArrayHelper::getColumn($data, 'photo'));
                                }

                                $names = array_filter($names);
                                $photos = array_filter($photos);
                                $finds = [];

                                if ($names) $finds[] = 'Имя';
                                if ($photos) $finds[] = 'Фото';

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
                    ]
                ]) ?>

                <hr>

                <h2>Лог авторизаций</h2>

                <?= GridView::widget([
                    'dataProvider' => $auth,
                    'columns' => [
                        'id',
                        [
                            'attribute' => 'site_id',
                            'value' => function (\app\models\UserAuthLog $model) {
                                return ArrayHelper::getValue($model, 'site.name');
                            }
                        ],
                        'ip',
                        'tm'
                    ]
                ]) ?>
            </div>
        </div>
    </div>
</div>
