<?php

/* @var $this \yii\web\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */

use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Wallet;

$this->title = 'Кошельки';

$total = array_sum(ArrayHelper::getColumn($dataProvider->models, 'balance'));
?>

<?= Html::tag('p', Html::a('Добавить кошелёк', ['wallets/create'], ['class' => 'btn btn-primary'])) ?>

<b>Всего: <?= Yii::$app->formatter->asCurrency($total, 'RUB') ?></b><br><br>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'rowOptions' => function($model) {
        if (is_null($model->tm_last_transaction_out)) return [];
        if (time() - strtotime($model->tm_last_transaction_out) > 60 * 60 * 48) return ['class' => 'danger'];
        return ['class' => 'success'];
    },
    'columns' => [
        [
            'attribute' => 'type_id',
            'header' => 'Тип / сайт',
            'content' => function($model) {
                $type = "";
                switch ($model->type_id) {
                    case Wallet::TYPE_YANDEX: $type = 'Яндекс.Деньги'; break;
                    case Wallet::TYPE_QIWI: $type = 'Qiwi кошелек'; break;
                }
                return join("<br />", [$type, ArrayHelper::getValue($model, ['site', 'name'])]);
            }
        ],
        [
            'attribute' => 'wallet_id',
            'header' => 'Кошелек / номер телефона',
            'content' => function($model) {
                $content = [$model->wallet_id];
                if($model->type_id == Wallet::TYPE_YANDEX) {
                    $content[] = $model->phone;
                }
                return join("<br />", $content);
            }
        ],
        [
            "header" => 'Логин / пароль',
            "content" => function($model) {
                return join("<br />", [$model->login, $model->password]);
            }
        ],
        [
            "attribute" => 'balance',
            "content" => function($model) {
                return Yii::$app->formatter->asCurrency($model->balance, 'RUB');
            }
        ],
        'tm_last_balance',
        [
            "header" => 'Приход / Расход',
            'content' => function($model) {
                return join("<br />", [Yii::$app->formatter->asRelativeTime($model->tm_last_transaction), Yii::$app->formatter->asRelativeTime($model->tm_last_transaction_out)]);
            }
        ],
        [
            "header" => 'Комментарий',
            'content' => function($model) {
                return nl2br($model->comment);
            }
        ],
        [
            'class' => ActionColumn::className(),
            'template' => '{view}',
        ],
    ],
]) ?>
