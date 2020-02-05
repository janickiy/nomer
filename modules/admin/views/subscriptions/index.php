<?php
/* @var $this \yii\web\View */

use yii\helpers\Html;

/* @var $dataProvider \yii\data\ActiveDataProvider */
?>

<?=\yii\grid\GridView::widget([
    "dataProvider" => $dataProvider,
    "columns" => [
        "id",
        [
            "attribute" => "user_id",
            "content" => function($model) {
                return Html::a($model->user_id, ["id" => $model->user_id, "users/view"]);
            }
        ],
        "transaction_id",
        "original_transaction_id",
        "tm_purchase:date",
        "tm_expires:date"
    ]
]);
