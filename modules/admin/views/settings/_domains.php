<?php

/* @var $this \yii\web\View */
/* @var $model \app\models\UrlFilter */
/* @var $dataProvider \yii\data\ActiveDataProvider */

use app\models\UrlFilter;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'url')->textInput() ?>
<?= $form->field($model, 'type')->dropDownList(UrlFilter::getTypes()) ?>
<?= Html::submitButton('Добавить', ['class' => 'btn btn-success']) ?>

<?php ActiveForm::end(); ?>

<hr>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        'url',
        [
            'attribute' => 'type',
            'value' => function(UrlFilter $model) {
                return UrlFilter::typeText($model->type);
            }
        ],
        [
            'format' => 'raw',
            'header' => 'Действия',
            'value' => function(UrlFilter $model) {
                return '<a href="' . Url::to(['settings/delete-domain', 'id' => $model->id]) . '" onclick="return confirm(\'Удалить домен?\')"><i class="icon-trash"></i></a>';
            }
        ]
    ]
]) ?>
