<?php
/* @var $searchRequest \app\models\SearchRequest */
use yii\helpers\Html;
use yii\helpers\Url;

?>
<div class="breadcrumbs">
    <ul class="breadcrumb">
        <?php if(\Yii::$app->devicedetect->isMobile() || \Yii::$app->devicedetect->isTablet()): ?>
            <li><?= Html::a('Главная', Url::home()) ?></li>
            <li>Поиск данных в Google</li>
        <?php else: ?>
            <li><?= Html::a('Главная', Url::home()) ?></li>
            <li>Поиск данных в Google</li>
        <?php endif; ?>
    </ul>
</div>

<div class="page-content">
    <div class="cont clfix">
        <h1>Поиск данных в Google</h1>
        <br />

        <div class="result resultNo" id="google">
            <div class="resultInner">
                <div class="parcent">0%</div>
                <div class="resultTitle ic7">Google<span class="sTime"></span></div>
                <div class="resultCont">
                    Идет поиск
                </div>
            </div>
        </div>
    </div>
</div>

<?php
if ($searchRequest->id) {
    $this->registerJs("NomerIoApp.socket().emit('searchgoogle', { id: " . $searchRequest->id . "})", \yii\web\View::POS_READY, "search");
}
?>
