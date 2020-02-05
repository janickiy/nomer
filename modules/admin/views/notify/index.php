<?php
/* @var $this \yii\web\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */

use yii\helpers\Html;

$this->title = "Уведомления в iOS";
?>

<?=Html::beginForm(["notify/index"]); ?>

<div class="form-group">
    <label for="whom">Кому отправлять уведомления</label>
    <?=Html::dropDownList("whom", "nosub", [
        "all"   => "Всем",
        "nosub" => "Без активных подписок"
    ], ["class" => "form-control"]);?>
</div>

<div class="form-group">
    <label for="message">Текст уведомления</label>
<?=Html::textInput("message", "", ["class" => "form-control"]); ?>
</div>
<div class="form-group">
    <label for="payload">Payload</label>
    <?=Html::textarea("payload", "", ["class" => "form-control"]); ?>
</div>
<button type="submit" class="btn btn-default">Отправить</button>
<?=Html::endForm();?>

<br><br>

<?=\yii\grid\GridView::widget([
    "dataProvider"  => $dataProvider,
    "columns"       => [
        "id",
        "tm_create",
        "message",
        [
            "header" => "Данные",
            "content" => function(\app\models\Notification $model) {
                return Html::tag("pre", print_r(\yii\helpers\Json::decode($model->payload), true));
            }
        ],
        [
            "header" => "Отправлено",
            "content" => function(\app\models\Notification $model) {
                return $model->getResults()->count(1);
            }
        ],
        [
            "header" => "Доставлено",
            "content" => function(\app\models\Notification $model) {
                return $model->getGoodResults()->count(1);
            }
        ]
    ]
]); ?>