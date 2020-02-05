<?php

/* @var $this \yii\web\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $registrations array */
/* @var $tm_start string */
/* @var $tm_end string */
/* @var $email string */
/* @var $registrationConfirms array */
/* @var $isVIP boolean */
/* @var $isAdmin boolean */
/* @var $withChecks boolean */

use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\DatePicker;
use app\models\User;
use app\models\Payment;

$this->title = 'Пользователи';

?>

<div class="portlet light bordered">
    <div class="portlet-title">
        <div class="caption">Фильтры</div>
    </div>
    <div class="portlet-body form">
        <?= Html::beginForm(['users/index'], 'GET') ?>
        <div class="input-group input-medium">
            <span class="input-group-addon">С</span>
            <?= DatePicker::widget([
                'name'  => 'tm_start',
                'value'  => $tm_start,
                'language' => 'ru',
                'dateFormat' => 'yyyy-MM-dd',
                'options' => ['class' => 'form-control input-small']
            ]) ?>
            <span class="input-group-addon">по</span>
            <?= DatePicker::widget([
                'name'  => 'tm_end',
                'value'  => $tm_end,
                'language' => 'ru',
                'dateFormat' => 'yyyy-MM-dd',
                'options' => ['class' => 'form-control input-small']
            ]) ?>
            <?= Html::hiddenInput('is_vip', $isVIP) ?>
            <?= Html::hiddenInput('is_admin', $isAdmin) ?>
            <?= Html::hiddenInput('with_checks', $withChecks) ?>
            <span class="input-group-addon"></span>
            <button type="submit" class="form-control btn btn-primary input-small">Показать</button>
        </div>
        <?= Html::endForm() ?>

        <hr>

        <?= Html::beginForm(['users/index'], 'GET') ?>
        <div class="input-group input-medium">
            <?= Html::textInput('email', $email, ['class' => 'form-control', 'style' => 'width: 370px;', 'placeholder' => 'Email пользователя']) ?>
            <?= Html::hiddenInput('is_vip', $isVIP) ?>
            <?= Html::hiddenInput('is_admin', $isAdmin) ?>
            <?= Html::hiddenInput('with_checks', $withChecks) ?>
            <span class="input-group-addon"></span>
            <button type="submit" class="form-control btn btn-primary input-small">Показать</button>
        </div>
        <?= Html::endForm() ?>

        <hr>

        <?= Html::a('VIP', ['users/index',
            'tm_start' => $tm_start,
            'tm_end' => $tm_end,
            'email' => $email,
            'is_vip' => !$isVIP,
            'is_admin' => $isAdmin,
            'with_checks' => $withChecks
        ], ['class' => $isVIP ? 'btn btn-success' : 'btn btn-primary', 'style' => 'width: 145px;']) ?>
        <?= Html::a('Администратор', ['users/index',
            'tm_start' => $tm_start,
            'tm_end' => $tm_end,
            'email' => $email,
            'is_vip' => $isVIP,
            'is_admin' => !$isAdmin,
            'with_checks' => $withChecks
        ], ['class' => $isAdmin ? 'btn btn-success' : 'btn btn-primary', 'style' => 'width: 170px;']) ?>
        <?= Html::a('Есть проверки', ['users/index',
            'tm_start' => $tm_start,
            'tm_end' => $tm_end,
            'email' => $email,
            'is_vip' => $isVIP,
            'is_admin' => $isAdmin,
            'with_checks' => !$withChecks
        ], ['class' => $withChecks ? 'btn btn-success' : 'btn btn-primary', 'style' => 'width: 220px;']) ?>
    </div>
</div>

<?php if (!$email): ?>
    <div class="row">
        <div class="col-md-4">
            <table class="table table-striped table-bordered">
                <tbody>
                <?php foreach ($registrations as $type): ?>
                    <tr>
                        <th><?= $type['source'] ?$type['source']: 'email' ?></th>
                        <td><?= $type['count'] ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'rowOptions' => function(User $model) {
        if ($model->is_confirm) return ['class' => 'success'];
    },
    'columns' => [
        [
            'attribute' => 'id',
            'format' => 'raw',
            'value' => function($model) {
                return Html::a($model->id, ['users/view', 'id' => $model->id]);
            }
        ],
        [
            'format' => 'raw',
            'attribute' => 'email',
            'value' => function(User $model) {
                $socials = [];

                foreach ($model->auth as $a) {
                    $link = null;

                    switch ($a->source) {
                        case 'vk':
                            $link = 'https://vk.com/id' . $a->source_id;
                            break;
                        case 'facebook':
                            $link = 'https://facebook.com/' . $a->source_id;
                            break;
                    }

                    if (is_null($link)) {
                        $source = $a->source;

                        if ($source == 'google') {
                            $source .= ' (' . count($model->contacts) . ')';
                        }

                        $socials[] = $source;
                    } else {
                        $socials[] = Html::a($a->source, $link);
                    }
                }

                $username = "";

                if(trim($model->email) != "") $username = $model->email;
                else {
                    if(preg_match("/-/", $model->uuid)) {
                        $username = "iOS (".$model->uuid.")";
                    } else {
                        $username = "Android (".$model->uuid.")";
                    }
                }

                return '<a href="' . Url::toRoute(['users/view', 'id' => $model->id]) . '">' . $username . '</a><br>' . join(', ', $socials);
            }
        ],
        'checks',
        [
            'header' => 'Общая сумма платежей',
            'content' => function(User $model) {
                return array_sum(array_map(function(Payment $payment) { return $payment->sum; }, $model->payments));
            }
        ],
        [
            'header' => 'Дата регистрации',
            'content' => function($model) {
                return join("<br />", [
                    \Yii::$app->formatter->asDate($model->tm_create, 'dd.MM.yyyy'),
                    $model->ip
                ]);
            }
        ],
        [
            'header' => 'Поисков<br>всего',
            'content' => function($model) {
                return count($model->requests);
            }
        ],
        [
            'header' => '%',
            'content' => function($model) {
                $index = 0;
                foreach($model->requests as $r) {
                    $index += array_sum(ArrayHelper::getColumn($r->results, 'index'));
                }
                return round(count($model->requests) ? ($index / count($model->requests)) : 0, 2);
            }
        ],
        [
            'header' => 'Поисков<br>за сегодня',
            'content' => function($model) {
                return count(array_filter($model->requests, function($r) {
                    return date('Y-m-d', strtotime($r->tm)) == date('Y-m-d');
                }));
            }
        ],
        [
            'header' => '%<br>за сегодня',
            'content' => function($model) {
                $index = 0;

                $requests = array_filter($model->requests, function($r) {
                    return date('Y-m-d', strtotime($r->tm)) == date('Y-m-d');
                });

                foreach($requests as $r) {
                    $index += array_sum(ArrayHelper::getColumn($r->results, 'index'));
                }

                return round(count($requests) ? ($index / count($requests)) : 0, 2);
            }
        ],
        'comment',
        [
            'class' => ActionColumn::className(),
            'template' => '{set-vip} {set-admin}',
            'buttons' => [
                'set-vip' => function ($url, $model, $key) {
                    return $model->is_vip ? Html::a('Убрать VIP', ['set-vip', 'id' => $model->id], ['class' => 'btn btn-danger']) : Html::a('Поставить VIP', ['set-vip', 'id' => $model->id], ['class' => 'btn btn-success']);
                },
                'set-admin' => function ($url, $model, $key) {
                    return $model->is_admin ? Html::a('Убрать админа', ['set-admin', 'id' => $model->id], ['class' => 'btn btn-danger']) : Html::a('Поставить админа', ['set-admin', 'id' => $model->id], ['class' => 'btn btn-success']);
                }
            ]
        ],
    ],
]);
