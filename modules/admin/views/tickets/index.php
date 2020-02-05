<?php

/* @var $this \yii\web\View */
/* @var $tickets \yii\data\ActiveDataProvider */
/* @var $ticketsNotRead \yii\data\ActiveDataProvider */

use yii\grid\ActionColumn;
use yii\helpers\ArrayHelper;
use \app\models\Ticket;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Список запросов';

echo \yii\grid\GridView::widget([
    "dataProvider" => $ticketsNotRead,
    "tableOptions" => [
        "class" => "tickets"
    ],
    "rowOptions" => function ($model, $key, $index, $grid) {
        return ["onclick" => "location.href='".Url::toRoute(["tickets/view", "id" => $model->id])."'", "class" => "ticketRow"];
    },
    "caption" => "Открытые тикеты без ответа администратора",
    "layout" => "{items}\n{summary}\n{pager}",
    "columns" => [
        "id",
        "tm_create",
        [
            'attribute' => 'user_id',
            'content' => function(Ticket $model) {
                $email = ArrayHelper::getValue($model, 'user.email');
                return $email?$email:"iOS пользователь";
            }
        ],
        [
            'attribute' => 'subject_id',
            'content' => function(Ticket $model) {
                return ArrayHelper::getValue(Ticket::SUBJECTS, $model->subject_id, '-');
            }
        ],
        [
            'attribute' => 'subject',
            'content' => function(Ticket $model) {
                return $model->subject;
            }
        ],
        [
            'attribute' => 'status',
            'content' => function(Ticket $model) {
                return ArrayHelper::getValue(Ticket::STATUSES, $model->status);
            }
        ],
        [
            'class' => ActionColumn::className(),
            'template' => '{close} {reopen} {delete}',
            'buttons' => [
                'close' => function ($url, $model, $key) {
                    return $model->status!=4?Html::a("Закрыть", $url, [
                        "class" => "btn btn-xs btn-warning",
                        "onclick" => "event.stopPropagation();"
                    ]):'';
                },
                'reopen' => function ($url, $model, $key) {
                    return $model->status==4?Html::a("Переоткрыть", $url, [
                        "class" => "btn btn-xs btn-warning",
                        "onclick" => "event.stopPropagation();"
                    ]):'';
                },
                'delete' => function ($url, $model, $key) {
                    return Html::a("Удалить", $url, [
                        "class" => "btn btn-xs btn-danger",
                        "onclick" => "event.stopPropagation();"
                    ]);
                }
            ]
        ],
    ]
]);?>
<br><br>
<?php echo \yii\grid\GridView::widget([
    "dataProvider" => $tickets,
    "tableOptions" => [
        "class" => "tickets"
    ],
    "rowOptions" => function ($model, $key, $index, $grid) {
        return ["onclick" => "location.href='".Url::toRoute(["tickets/view", "id" => $model->id])."'", "class" => "ticketRow"];
    },
    "caption" => "Все остальные тикеты",
    "layout" => "{items}\n{summary}\n{pager}",
    "columns" => [
        "id",
        "tm_create",
        [
            'attribute' => 'user_id',
            'content' => function(Ticket $model) {
                $email = ArrayHelper::getValue($model, 'user.email');
                return $email?$email:"iOS пользователь";
            }
        ],
        [
            'attribute' => 'subject_id',
            'content' => function(Ticket $model) {
                return ArrayHelper::getValue(Ticket::SUBJECTS, $model->subject_id, "-");
            }
        ],
        [
            'attribute' => 'subject',
            'content' => function(Ticket $model) {
                return $model->subject;
            }
        ],
        [
            'attribute' => 'status',
            'content' => function(Ticket $model) {
                return ArrayHelper::getValue(Ticket::STATUSES, $model->status);
            }
        ],
        [
            'class' => ActionColumn::className(),
            'template' => '{close} {reopen} {delete}',
            'buttons' => [
                'close' => function ($url, $model, $key) {
                    return $model->status!=4?Html::a("Закрыть", $url, [
                        "class" => "btn btn-xs btn-warning",
                        "onclick" => "event.stopPropagation();"
                    ]):'';
                },
                'reopen' => function ($url, $model, $key) {
                    return $model->status==4?Html::a("Переоткрыть", $url, [
                        "class" => "btn btn-xs btn-warning",
                        "onclick" => "event.stopPropagation();"
                    ]):'';
                },
                'delete' => function ($url, $model, $key) {
                    return Html::a("Удалить", $url, [
                        "class" => "btn btn-xs btn-danger",
                        "onclick" => "event.stopPropagation();"
                    ]);
                }
            ]
        ],
    ]
]);
?>
