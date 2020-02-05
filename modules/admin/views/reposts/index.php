<?php
/* @var $this \yii\web\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */

use yii\grid\GridView;
use yii\helpers\Html;

?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'rowOptions' => function(\app\models\Repost $model) {
        if ($model->status == 0) return ['class' => 'danger'];
        return [];
    },
    'columns' => [
        "id",
        "tm",
        [
            "attribute" => "user_id",
            "content" => function(\app\models\Repost $model) {
                return Html::a($model->user->email, ["users/view", "id" => $model->user_id]);
            }
        ],
        [
            "attribute" => "vk_id",
            "content" => function(\app\models\Repost $model) {
                $link = "https://vk.com/id".$model->vk_id;
                return Html::a($link, $link);
            }
        ],
        "sms_count",
    ]
]) ?>
