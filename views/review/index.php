<?php
/* @var $this \yii\web\View */
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = "Пополнение счета";

$site = \app\models\Site::find()->where(["name" => \Yii::$app->request->hostName])->one();

?>

<div class="breadcrumbs">
    <ul class="breadcrumb">
        <?php if(\Yii::$app->devicedetect->isMobile() || \Yii::$app->devicedetect->isTablet()): ?>
            <li><?= Html::a('Главная', Url::home()) ?></li>
            <li>Оставить отзыв</li>
        <?php else: ?>
            <li><?= Html::a('Главная', Url::home()) ?></li>
            <li>Оставить отзыв</li>
        <?php endif; ?>
    </ul>
</div>

<div class="page-content">
    <div class="cont clfix">
        <h1>Оставить отзыв</h1>
            <!-- Put this script tag to the <head> of your page -->
            <script type="text/javascript" src="//vk.com/js/api/openapi.js?146"></script>

            <script type="text/javascript">
                VK.init({apiId: <?=$site->vk_id;?>, onlyWidgets: true});
            </script>

            <!-- Put this div tag to the place, where the Comments block will be -->
            <div id="vk_comments"></div>
            <script type="text/javascript">
                VK.Widgets.Comments("vk_comments", {limit: 20, attach: "*", autoPublish: 1}, 777);
            </script>
    </div>
</div>