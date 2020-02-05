<?php
/* @var $this \yii\web\View */
/* @var $subs array */
/* @var $subId int */
/* @var $tm_start string */
/* @var $tm_end string */

use yii\helpers\Html;

/* @var $dataProvider \yii\data\ActiveDataProvider */

$this->title = "Отчет apple";
?>


<?=Html::beginForm('', 'get'); ?>
<?=Html::dropDownList("sub_id", $subId, $subs, ["prompt" => "Выберите подписку"]);?>

    с <?=\yii\jui\DatePicker::widget(["name" => "tm_start", "class" => "form-control input-small", "value" => $tm_start, "dateFormat" => "y-MM-dd"]); ?>
    по <?=\yii\jui\DatePicker::widget(["name" => "tm_end", "class" => "form-control input-small", "value" => $tm_end, "dateFormat" => "y-MM-dd"]); ?>
    <?=Html::submitButton("Показать", ["class" => "btn btn-primary"]); ?>
<?=Html::endForm(); ?>

<?=\yii\grid\GridView::widget([
    "dataProvider" => $dataProvider,
    "columns" => [
        "event_date",
        "event",
        "subscription_name",
        "original_start_date",
        "cancellation_reason",
        "quantity"
    ]
]);?>
