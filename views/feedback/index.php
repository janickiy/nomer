<?php
/* @var $this \yii\web\View */
/* @var $model \app\models\Ticket */
/* @var $ticketsDataProvider \yii\data\ActiveDataProvider */

/* @var $ticketsClosedDataProvider \yii\data\ActiveDataProvider */

use yii\grid\ActionColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use \app\models\Ticket;

$this->title = \Yii::$app->name . ' - обратная связь';
?>
<div class="breadcrumbs">
    <ul class="breadcrumb">
        <?php if (\Yii::$app->devicedetect->isMobile() || \Yii::$app->devicedetect->isTablet()): ?>
            <li><?= Html::a('Главная', Url::home()) ?></li>
            <li>Обратная связь</li>
        <?php else: ?>
            <li><?= Html::a('Главная', Url::home()) ?></li>
            <li>Обратная связь</li>
        <?php endif; ?>
    </ul>
</div>

<div class="page-content">
    <div class="cont clfix">
        <h1>Обратная связь</h1>

        <?php if (\Yii::$app->getUser()->isGuest): ?>
            <h2>Что бы с нами связаться, вам нужно авторизоваться!</h2>
            <p align="center"><a href="#signup" class="button" style="width: 300px">Войти</a></p>
        <?php else: ?>
            <p style="text-align: center"><?= Html::a("Создать запрос", ["feedback/new"], ["class" => "newticket"]); ?></p>
            <br>

            <?php if (!$ticketsDataProvider->getTotalCount() && !$ticketsClosedDataProvider->getTotalCount()): ?>
                <p class="qiwi-descr"><span>У вас ещё нет ниодного тикета.</span></p>
            <?php endif; ?>

            <?php if ($ticketsDataProvider->getTotalCount()): ?>
                <?= \yii\grid\GridView::widget([
                    "dataProvider" => $ticketsDataProvider,
                    "tableOptions" => [
                        "class" => "tickets"
                    ],
                    "rowOptions" => function ($model, $key, $index, $grid) {
                        return ["onclick" => "location.href='" . Url::toRoute(["feedback/view", "id" => $model->id]) . "'", "class" => "ticketRow"];
                    },
                    "layout" => "{items}\n{summary}\n{pager}",
                    "caption" => "Открытые запросы",
                    "columns" => [
                        "id",
                        "tm_create",
                        [
                            'attribute' => 'subject_id',
                            'content' => function (Ticket $model) {
                                return Ticket::SUBJECTS[$model->subject_id];
                            }
                        ],
                        [
                            'attribute' => 'subject',
                            'content' => function (Ticket $model) {
                                return $model->subject;
                            }
                        ],
                        [
                            'attribute' => 'status',
                            'content' => function (Ticket $model) {
                                if(in_array($model->status, [6,7])) {
                                    $model->status = 1;
                                }
                                return ArrayHelper::getValue(Ticket::STATUSES, $model->status);
                            }
                        ]
                    ]
                ]); ?>
                <br/>
            <?php endif; ?>
            <?php if ($ticketsClosedDataProvider->getTotalCount()): ?>
                <?= \yii\grid\GridView::widget([
                    "dataProvider" => $ticketsClosedDataProvider,
                    "tableOptions" => [
                        "class" => "tickets"
                    ],
                    "layout" => "{items}\n{summary}\n{pager}",
                    "caption" => "Закрытые запросы",
                    "rowOptions" => function ($model, $key, $index, $grid) {
                        return ["onclick" => "location.href='" . Url::toRoute(["feedback/view", "id" => $model->id]) . "'", "class" => "ticketRow"];
                    },
                    "columns" => [
                        "id",
                        "tm_create",
                        [
                            'attribute' => 'subject_id',
                            'content' => function (Ticket $model) {
                                return Ticket::SUBJECTS[$model->subject_id];
                            }
                        ],
                        [
                            'attribute' => 'subject',
                            'content' => function (Ticket $model) {
                                return $model->subject;
                            }
                        ],
                        [
                            'attribute' => 'status',
                            'content' => function (Ticket $model) {
                                return ArrayHelper::getValue(Ticket::STATUSES, $model->status);
                            }
                        ]
                    ]
                ]); ?>
            <?php endif; ?>

        <?php endif; ?>

        <?php if ($success = \Yii::$app->session->getFlash("success", false)): ?>
            <p><?= $success; ?></p>
        <?php else: ?>

        <?php endif; ?>
    </div>
</div>
