<?php
/* @var $this \yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;

$sum = \Yii::$app->request->get("sum");

$order = new \app\models\WebmoneyOrder();
$order->sum = $sum;
$order->user_id = \Yii::$app->getUser()->getId();
$order->save();



$cost = \Yii::$app->params["cost"];

if($sum >= 9000) {
    $cost = 30;
} elseif($sum >= 3400) {
    $cost = 34;
} elseif($sum >= 1170) {
    $cost = 39;
} elseif($sum >= 440) {
    $cost = 44;
}
$checks = floor($sum / $cost);

$this->title = "Выбор способа оплаты";
?>

<div class="breadcrumbs">
    <ul class="breadcrumb">
        <?php if(\Yii::$app->devicedetect->isMobile() || \Yii::$app->devicedetect->isTablet()): ?>
            <li><?= Html::a('Главная', Url::home()) ?></li>
            <li><?= Html::a("Покупка проверок", Url::toRoute(['pay/index'])) ?></li>
        <?php else: ?>
            <li><?= Html::a('Главная', Url::home()) ?></li>
            <li><?= Html::a("Покупка проверок", Url::toRoute(['pay/index'])) ?></li>
            <li>Выбор способа оплаты</li>
        <?php endif; ?>
    </ul>
</div>

<div class="page-content">
    <div class="cont clfix">
        <h1>Выберите способ оплаты</h1>
        <p>Вы оплачиваете <?=$sum;?> р. за <?=\Yii::t('app', '{n,plural,=0{проверок} =1{1 проверку} one{# проверка} few{# проверки} many{# проверок} other{# проверки}}', ['n' => $checks]);?></p>
        <br />
        <ul class="payment-methods">
            <li>
                <iframe src="https://api.paymentwall.com/api/subscription/?key=70996437da7350622d6a8f7a96e0e4fb&uid=<?=\Yii::$app->getUser()->getId();?>&widget=p4_1" width="371" height="450" frameborder="0"></iframe>
            </li>
            <li>
                Интеркасса
                <form id="payment" name="payment" method="post" action="https://sci.interkassa.com/" enctype="utf-8">

                    <input type="hidden" name="ik_co_id" value="58dce08a3b1eaf66228b4569" />

                    <input type="hidden" name="ik_pm_no" value="ID_4233" />

                    <input type="hidden" name="ik_am" value="<?=$sum;?>" />

                    <input type="hidden" name="ik_cur" value="RUB" />

                    <input type="hidden" name="ik_desc" value="Пополнение баланса в проекте <?=\Yii::$app->name;?>" />

                    <input type="hidden" name="ik_act" value="payways" />

                    <input type="hidden" name="ik_pw_on" value="visa,yandexmoney,webmoney_merchant,w1_merchant_usd,privat24" />

                    <input type="hidden" name="ik_suc_u" value="https://<?=\Yii::$app->name;?>/payments/interkassa-success" />

                    <input type="hidden" name="ik_suc_m" value="post" />

                    <input type="hidden" name="ik_fal_u" value="https://<?=\Yii::$app->name;?>/payments/interkassa-fail" />

                    <input type="hidden" name="ik_fal_m" value="post" />

                    <input type="hidden" name="ik_pnd_u" value="https://<?=\Yii::$app->name;?>/payments/interkassa-wait" />

                    <input type="hidden" name="ik_pnd_m" value="post" />

                    <input type="hidden" name="ik_exp" value="2017-03-31" />

                    <input type="hidden" name="ik_loc" value="ru" />

                    <input type="hidden" name="ik_enc" value="utf-8" />

                    <input type="hidden" name="ik_int" value="json" />

                    <input type="hidden" name="ik_am_t" value="invoice" />

                    <?=Html::submitButton("Выбрать"); ?>

                </form>
            </li>
            <li>Кредитная карта
                <?= Html::beginForm('https://money.yandex.ru/quickpay/confirm.xml', 'post', ['id' => 'paymentform']); ?>
                <input type="hidden" value="AC" name="paymentType">
                <input type="hidden" value="<?= \Yii::$app->getUser()->id; ?>" name="label">
                <input type="hidden" value="410014057045840" name="receiver">

                <input type="hidden" value="shop" name="quickpay-form">
                <input type="hidden" value="<?= Url::toRoute(['pay/methods', 'sum' => $sum], 'https'); ?>" name="referer">
                <input type="hidden" value="true" name="is-inner-form">
                <input type="hidden" value="Пополнение счета в <?= \Yii::$app->name; ?>" name="targets">
                <input type="hidden" value="<?= Url::toRoute(['pay/success'], 'https'); ?>"
                       name="successURL">
                <input type="hidden" value="<?= Url::toRoute(['pay/methods', 'sum' => $sum], 'https'); ?>"
                       name="quickpay-back-url">

                <?= Html::hiddenInput('sum', $sum); ?>

                <?=Html::submitButton("Выбрать"); ?>
                <?=Html::endForm(); ?>
            </li>
            <li>Яндекс-деньги <?= Html::beginForm('https://money.yandex.ru/quickpay/confirm.xml', 'post', ['id' => 'paymentform']); ?>
                <input type="hidden" value="PC" name="paymentType">
                <input type="hidden" value="<?= \Yii::$app->getUser()->id; ?>" name="label">
                <input type="hidden" value="410014057045840" name="receiver">

                <input type="hidden" value="shop" name="quickpay-form">
                <input type="hidden" value="<?= Url::toRoute(['pay/methods', 'sum' => $sum], 'https'); ?>" name="referer">
                <input type="hidden" value="false" name="is-inner-form">
                <input type="hidden" value="Пополнение счета в <?= \Yii::$app->name; ?>" name="targets">
                <input type="hidden" value="<?= Url::toRoute(['pay/success'], 'https'); ?>"
                       name="successURL">
                <input type="hidden" value="<?= Url::toRoute(['pay/methods', 'sum' => $sum], 'https'); ?>"
                       name="quickpay-back-url">

                <?= Html::hiddenInput('sum', $sum); ?>

                <?=Html::submitButton("Выбрать"); ?>
                <?=Html::endForm(); ?>
            </li>
            <li style="display: none;">Со счета мобильного телефона <?= Html::beginForm('https://money.yandex.ru/quickpay/confirm.xml', 'post', ['id' => 'paymentform']); ?>
                <input type="hidden" value="MC" name="paymentType">
                <input type="hidden" value="<?= \Yii::$app->getUser()->id; ?>" name="label">
                <input type="hidden" value="410014057045840" name="receiver">

                <input type="hidden" value="shop" name="quickpay-form">
                <input type="hidden" value="<?= Url::toRoute(['pay/methods', 'sum' => $sum], 'https'); ?>" name="referer">
                <input type="hidden" value="false" name="is-inner-form">
                <input type="hidden" value="Пополнение счета в <?= \Yii::$app->name; ?>" name="targets">
                <input type="hidden" value="<?= Url::toRoute(['pay/success'], 'https'); ?>"
                       name="successURL">
                <input type="hidden" value="<?= Url::toRoute(['pay/methods', 'sum' => $sum], 'https'); ?>"
                       name="quickpay-back-url">

                <?= Html::hiddenInput('sum', $sum); ?>

                <?=Html::submitButton("Выбрать"); ?>
                <?=Html::endForm(); ?>
            </li>
            <li>QIWI с терминала <a href="<?=Url::toRoute(["pay/qiwi", "sum" => $sum]);?>">Выбрать</a></li>
            <li>QIWI с кошелька <a href="<?=Url::toRoute(["pay/qiwi", "sum" => $sum]);?>">Выбрать</a></li>
            <li>Сбербанк <span>Скоро</span></li>
            <li>WebMoney
                <?php echo Html::beginForm('https://merchant.webmoney.ru/lmi/payment.asp'),
                Html::hiddenInput('LMI_PAYMENT_AMOUNT', $sum),
                Html::hiddenInput('LMI_PAYMENT_DESC', 'order '.$order->id),
                Html::hiddenInput('LMI_PAYMENT_NO', $order->id),
                Html::hiddenInput('LMI_PAYEE_PURSE', 'R626242660214'),
                Html::hiddenInput('LMI_RESULT_URL', Url::toRoute(["pay/webmoney-result"], 'https')),
                Html::hiddenInput('LMI_FAIL_URL', Url::toRoute(["pay/fail"], 'https')),
                Html::hiddenInput('LMI_SUCCESS_URL', Url::toRoute(["pay/success"], 'https')),
                Html::hiddenInput('LMI_SIM_MODE', 1);
                ?>

                <?=Html::submitButton("Выбрать"); ?>

                <?php echo Html::endForm(); ?>
            </li>
        </ul>
    </div>
</div>