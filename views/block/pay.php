<?php

/* @var $this \yii\web\View */
/* @var $phone string */
/* @var $price int */

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\ConfigHelper;

$this->title = \Yii::$app->name . ' - VIP исключение номера из базы';

?>

<div class="breadcrumbs">
    <ul class="breadcrumb">
        <?php if (\Yii::$app->devicedetect->isMobile() || \Yii::$app->devicedetect->isTablet()): ?>
            <li><?= Html::a('Главная', Url::home()) ?></li>
            <li>VIP исключение номера из базы</li>
        <?php else: ?>
            <li><?= Html::a('Главная', Url::home()) ?></li>
            <li>VIP исключение номера из базы</li>
        <?php endif; ?>
    </ul>
</div>


<div class="page-content">
    <div class="cont clfix">
        <h1>Ваш номер успешно заблокирован!</h1>
        <h2>VIP исключение номера из базы</h2>
        При поиске вашего номера пользователь вместо сообщения о его блокировке получит пустую страницу результатов поиска.
        Также вам будет отправлено SMS-уведомление, в котором будет указан номер искавшего информацию о вас пользователя.
        <br><br>
        <h3>Стоимость услуги составляет 299 рублей.</h3>
        <br>
        <ul class="payment-methods">
            <li><div><img src="/img/pay/payments_card.png"></div> Кредитная карта
                <?= Html::beginForm('https://money.yandex.ru/quickpay/confirm.xml', 'post', ['id' => 'paymentform']); ?>
                <input type="hidden" value="AC" name="paymentType">
                <input type="hidden" value="block-<?= $phone ?>-<?= \Yii::$app->getUser()->id ? \Yii::$app->getUser()->id : 0 ?>-<?= ConfigHelper::getInstance()->getSiteId() ?>" name="label">
                <input type="hidden" value="<?=ConfigHelper::getInstance()->getYandexMoney();?>" name="receiver">
                <input type="hidden" value="shop" name="quickpay-form">
                <input type="hidden" value="Блокировка номера <?= $phone ?>" name="targets">
                <input type="hidden" value="<?= Url::toRoute(['pay/success'], 'https'); ?>"
                       name="successURL">

                <?= Html::hiddenInput('sum', $price); ?>
                <?=Html::submitButton("Выбрать", ['class' => 'button']); ?>
                <?=Html::endForm(); ?>
            </li>
            <li><div><img src="/img/pay/payments_yandex.png"></div> Яндекс-деньги <?= Html::beginForm('https://money.yandex.ru/quickpay/confirm.xml', 'post', ['id' => 'paymentform']); ?>
                <input type="hidden" value="PC" name="paymentType">
                <input type="hidden" value="block-<?= $phone ?>-<?= \Yii::$app->getUser()->id ? \Yii::$app->getUser()->id : 0 ?>-<?= ConfigHelper::getInstance()->getSiteId() ?>" name="label">
                <input type="hidden" value="<?=ConfigHelper::getInstance()->getYandexMoney();?>" name="receiver">

                <input type="hidden" value="shop" name="quickpay-form">
                <input type="hidden" value="Блокировка номера <?= $phone ?>" name="targets">
                <input type="hidden" value="<?= Url::toRoute(['pay/success'], 'https'); ?>"
                       name="successURL">

                <?= Html::hiddenInput('sum', $price); ?>

                <?=Html::submitButton("Выбрать", ['class' => 'button']); ?>
                <?=Html::endForm(); ?>
            </li>
            <li><div><img src="/img/pay/payments_qiwi_terminal.png"></div> QIWI с терминала
                <?= Html::beginForm(Url::toRoute(["pay/qiwi-block"]), 'get', ['id' => 'paymentform']); ?>
                <?= Html::hiddenInput('price', $price); ?>

                <?=Html::submitButton("Выбрать", ['class' => 'button']); ?>
                <?=Html::endForm(); ?>
            <li><div><img src="/img/pay/payments_qiwi.png"></div> QIWI с кошелька
                <?= Html::beginForm(Url::toRoute(["pay/qiwi-block"]), 'get', ['id' => 'paymentform']); ?>
                <?= Html::hiddenInput('price', $price); ?>

                <?=Html::submitButton("Выбрать", ['class' => 'button']); ?>
                <?=Html::endForm(); ?>
            </li>
        </ul>
        <hr>
        <a class="button" style="width: 100%;" href="<?= Url::to(['block/decline-pay']) ?>">Нет, спасибо</a>
    </div>
</div>
