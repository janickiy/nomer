<?php
/* @var $this \yii\web\View */

use app\models\RequestResult;
use app\models\ResultCache;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/* @var $operator array */
/* @var $searchRequest \app\models\SearchRequest */

$inst = RequestResult::find()->where(["request_id" => $searchRequest->id, "type_id" => ResultCache::TYPE_INSTAGRAM])->one();
$fb = RequestResult::find()->where(["request_id" => $searchRequest->id, "type_id" => ResultCache::TYPE_FACEBOOK])->one();
$vk2012 = RequestResult::find()->where(["request_id" => $searchRequest->id, "type_id" => ResultCache::TYPE_VK_2012])->one();
$vkOpen = RequestResult::find()->where(["request_id" => $searchRequest->id, "type_id" => ResultCache::TYPE_VK_OPEN])->one();
$avito = RequestResult::find()->where(["request_id" => $searchRequest->id, "type_id" => ResultCache::TYPE_AVITO])->one();
$avitoItems = 0;
if($avito) {
    $avitoData = \yii\helpers\Json::decode($avito->data);
    $avitoItems = count($avitoData);
}
$avinfo = RequestResult::find()->where(["request_id" => $searchRequest->id, "type_id" => ResultCache::TYPE_AVINFO_API])->one();
$avinfoItems = 0;
if($avinfo) {
    $avinfoData = \yii\helpers\Json::decode($avinfo->data);
    if(isset($autoResult["auto"])) {
        $avinfoItems = count($avinfoData["auto"]);
    }
}

$autoItems = 0;

$antiparkon = RequestResult::find()->where(["request_id" => $searchRequest->id, "type_id" => ResultCache::TYPE_ANTIPARKON])->one();
if($antiparkon) {
    $antiparkonData = \yii\helpers\Json::decode($antiparkon->data);
    $autoItems += count($antiparkonData);
}

$gibdd = RequestResult::find()->where(["request_id" => $searchRequest->id, "type_id" => ResultCache::TYPE_GIBDD])->one();
if($gibdd) {
    $gibddData = \yii\helpers\Json::decode($gibdd->data);
    $autoItems += count($gibddData);
}

$names = [];
$numbaster = RequestResult::find()->where(["request_id" => $searchRequest->id, "type_id" => ResultCache::TYPE_NUMBUSTER])->one();
if($numbaster) {
    $numbasterData = \yii\helpers\Json::decode($numbaster->data);
    $names[] = count($numbasterData);
}

$truecaller = RequestResult::find()->where(["request_id" => $searchRequest->id, "type_id" => ResultCache::TYPE_TRUECALLER])->one();
if($truecaller) {
    $truecallerData = \yii\helpers\Json::decode($truecaller->data);
    $names[] = count($truecallerData);
}

$viber = RequestResult::find()->where(["request_id" => $searchRequest->id, "type_id" => ResultCache::TYPE_VIBER])->one();
if($viber) {
    if($viber->index > 0) {
        $names[] = 1;
    }
}

$telegram = RequestResult::find()->where(["request_id" => $searchRequest->id, "type_id" => ResultCache::TYPE_TELEGRAM])->one();
if($telegram) {
    if($telegram->index > 0) {
        $names[] = 1;
    }

}

?>

<div class="searchStatusInner searchFinished">
    <?php if($operator): ?>
        Поиск завершен, <?=\yii\helpers\ArrayHelper::getValue($operator, "operator");?>(<?=\yii\helpers\ArrayHelper::getValue($operator, "region");?>)
    <?php else: ?>
        Поиск завершен.
    <?php endif; ?>
</div>

<div class="we-can-found">
    <p>По данному номеру телефона мы нашли<br>следующую информацию:</p>

    <div>
        <div class="--left">
            <ul>
                <?php if(ArrayHelper::getValue($fb, "index", 0) > 0): ?>
                    <li><img src="/img/free/fb.png" width="32"><span>Аккаунт на Facebook</span></li>
                <?php endif; ?>
                <?php if(ArrayHelper::getValue($vk2012, "index", 0) > 0 || ArrayHelper::getValue($vkOpen, "index", 0) > 0): ?>
                    <li><img src="/img/free/vk.png" width="32"><span>Анкету Вконтакте</span></li>
                <?php endif; ?>
                <?php if(ArrayHelper::getValue($inst, "index", 0) > 0): ?>
                    <li><img src="/img/Logo-instagram.png" width="32"><span>Инстаграмм человека</span></li>
                <?php endif; ?>
                <?php foreach ($names as $ni => $n):?>
                    <?php if(!$ni): ?>
                        <li><span>Имя из чужой телефонной книги</span></li>
                    <?php else: ?>
                        <li><span>Ещё имя из чужой телефонной книги</span></li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="--right">
            <ul>
                <?php if($avitoItems > 0): ?>
                    <li><img src="/img/free/avito.png" width="32"><span><?=\Yii::t('app', '{n,plural,=0{Объявлений нет} =1{Одно объявление} one{# объявление} few{# объявления} many{# объявлений} other{# объявления}}', ['n' => $avitoItems]);?> на Avito</span></li>
                <?php endif; ?>
                <?php if($avinfoItems > 0): ?>
                    <li><img src="/img/free/autoru.png" width="32" style="margin-top: 8px;"><span><?=\Yii::t('app', '{n,plural,=0{Автомобилей нет} =1{Один автомобиль} one{# автомобиль} few{# автомобиля} many{# автомобилей} other{# автомобиля}}', ['n' => $avinfoItems]);?> которые продавались на auto.ru</span></li>
                <?php endif; ?>
                <?php if($autoItems > 0): ?>
                    <li><img src="/img/free/cars.png" width="32" style="margin-top: 8px;"><span><?=\Yii::t('app', '{n,plural,=0{Автомобилей нет} =1{Один автомобиль} one{# автомобиль} few{# автомобиля} many{# автомобилей} other{# автомобиля}}', ['n' => $autoItems]);?> на которых ездил человек</span></li>
                <?php endif; ?>
                <ul>
        </div>
    </div>

    <p>Чтобы увидеть эти данные. вам необходимо пополнить счёт и выполнить платную проверку</p>

    <p>

        <?php if ($searchRequest->user_id): ?>
            <a href="<?= Url::toRoute(["pay/index"]); ?>" class="button">Купить информацию
                за <?= \Yii::$app->params["cost"]; ?> руб.</a>
        <?php else: ?>
            <a href="#signup" class="button">Регистрация / Вход</a>
        <?php endif; ?>
    </p>
</div>

