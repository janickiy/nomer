<?php

/* @var $this \yii\web\View */
/* @var $model \app\models\Telegram */
/* @var $dataProvider \yii\data\ActiveDataProvider */

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\Telegram;

$this->title = 'Telegram';

?>

<?php $form = ActiveForm::begin(); ?>
<?= $form->field($model, 'host')->textInput() ?>
<?= $form->field($model, 'port')->textInput() ?>
<?= Html::submitButton('Добавить', ['class' => 'btn btn-success']) ?>

<?php ActiveForm::end(); ?>

<hr>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        'host',
        'port',
        [
            'attribute' => 'status',
            'value' => function (Telegram $model) {
                return Telegram::getStatusName($model->status);
            }
        ],
        'tm_last',
        [
            'format' => 'raw',
            'header' => 'Действия',
            'value' => function(Telegram $model) {
                return '<a href="' . Url::to(['accounts/delete-telegram', 'id' => $model->id]) . '" onclick="return confirm(\'Удалить инстанс?\')"><i class="icon-trash"></i></a>';
            }
        ]
    ]
]) ?>
