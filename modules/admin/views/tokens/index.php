<?php

/* @var $this \yii\web\View */
/* @var $model \app\models\Token */
/* @var $dataProvider \yii\data\ActiveDataProvider */

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\Token;

$this->title = 'Токены';

?>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'token')->textInput() ?>
<?= $form->field($model, 'type')->dropDownList(Token::getTypes()) ?>
<?= $form->field($model, 'server_id')->textInput(['type' => 'number']) ?>
<?= Html::submitButton('Добавить', ['class' => 'btn btn-success']) ?>

<?php ActiveForm::end(); ?>

<hr>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        [
            'attribute' => 'type',
            'value' => function (Token $model) {
                return Token::getTypeName($model->type);
            }
        ],
        'server_id',
        'token',
        [
            'attribute' => 'status',
            'value' => function (Token $model) {
                return Token::getStatusName($model->status);
            }
        ],
        'tm_ban',
        [
            'format' => 'raw',
            'header' => 'Действия',
            'value' => function(Token $model) {
                return '<a href="' . Url::to(['tokens/delete', 'id' => $model->id]) . '" onclick="return confirm(\'Удалить токен?\')"><i class="icon-trash"></i></a>';
            }
        ]
    ]
]) ?>
