<?php
/* @var $this \yii\web\View */

use \app\components\ConfigHelper;
use app\components\CostsHelper;
use app\models\Site;
use yii\helpers\Html;
use yii\helpers\Url;

$sum = \Yii::$app->request->get("sum");

if($sum < 0) {$sum = -$sum;}

$order = new \app\models\WebmoneyOrder();
$order->sum = $sum;
$order->site_id = ConfigHelper::getInstance()->getSiteId();
$order->user_id = \Yii::$app->getUser()->getId();
$order->save();

//$cost = \Yii::$app->params["cost"];
$site = \app\models\Site::find()->where(["name" => $_SERVER["HTTP_HOST"]])->one();

$cost = CostsHelper::getCost(1, $site->id);

if($sum >= CostsHelper::getCostTotal(500, $site->id)) {
    $cost = CostsHelper::getCost(500, $site->id);
} elseif($sum >= CostsHelper::getCostTotal(300, $site->id)) {
    $cost = CostsHelper::getCost(300, $site->id);
} elseif($sum >= CostsHelper::getCostTotal(100, $site->id)) {
    $cost = CostsHelper::getCost(100, $site->id);
} elseif($sum >= CostsHelper::getCostTotal(50, $site->id)) {
    $cost = CostsHelper::getCost(50, $site->id);
} elseif($sum >= CostsHelper::getCostTotal(20, $site->id)) {
    $cost = CostsHelper::getCost(20, $site->id);
} elseif($sum >= CostsHelper::getCostTotal(10, $site->id)) {
    $cost = CostsHelper::getCost(10, $site->id);
}
$checks = floor((float)$sum / $cost);

$this->title = "Выбор способа оплаты";

$site = Site::find()->where(["name" => $_SERVER["HTTP_HOST"]])->one();
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
        <p class="info">Вы оплачиваете <span class="sum"><?=$sum;?> руб</span> за <span class="checks"><?=\Yii::t('app', '{n,plural,=0{проверок} =1{1 проверку} one{# проверка} few{# проверки} many{# проверок} other{# проверки}}', ['n' => $checks]);?></span></p>
        <br />
        <ul class="payment-methods">
            <li><div><img src="/img/pay/payments_card.png"></div> Кредитная карта
                <?= Html::beginForm('https://money.yandex.ru/quickpay/confirm.xml', 'post', ['id' => 'paymentform']); ?>
                <input type="hidden" value="AC" name="paymentType">
                <input type="hidden" value="<?= \Yii::$app->getUser()->id; ?>-<?=ConfigHelper::getInstance()->getSiteId();?>" name="label">
                <input type="hidden" value="<?=ConfigHelper::getInstance()->getYandexMoney();?>" name="receiver">
                <input type="hidden" value="shop" name="quickpay-form">
                <input type="hidden" value="Пополнение счета в <?= \Yii::$app->name; ?> (#<?= \Yii::$app->getUser()->id; ?>)" name="targets">
                <input type="hidden" value="<?= Url::toRoute(['pay/success'], 'https'); ?>"
                       name="successURL">

                <?= Html::hiddenInput('sum', $sum); ?>
                <?=Html::submitButton("Выбрать", ['class' => 'button']); ?>
                <?=Html::endForm(); ?>
            </li>
            <li><div><img src="/img/pay/payments_yandex.png"></div> Яндекс-деньги <?= Html::beginForm('https://money.yandex.ru/quickpay/confirm.xml', 'post', ['id' => 'paymentform']); ?>
                <input type="hidden" value="PC" name="paymentType">
                <input type="hidden" value="<?= \Yii::$app->getUser()->id; ?>-<?=ConfigHelper::getInstance()->getSiteId();?>" name="label">
                <input type="hidden" value="<?=ConfigHelper::getInstance()->getYandexMoney();?>" name="receiver">

                <input type="hidden" value="shop" name="quickpay-form">
                <input type="hidden" value="Пополнение счета в <?= \Yii::$app->name; ?> (#<?= \Yii::$app->getUser()->id; ?>)" name="targets">
                <input type="hidden" value="<?= Url::toRoute(['pay/success'], 'https'); ?>"
                       name="successURL">

                <?= Html::hiddenInput('sum', $sum); ?>

                <?=Html::submitButton("Выбрать", ['class' => 'button']); ?>
                <?=Html::endForm(); ?>
            </li>
            <li><div><img src="/img/pay/payments_qiwi_terminal.png"></div> QIWI с терминала
                    <?= Html::beginForm(Url::toRoute(["pay/qiwi"]), 'get', ['id' => 'paymentform']); ?>
                    <?= Html::hiddenInput('sum', $sum); ?>

                    <?=Html::submitButton("Выбрать", ['class' => 'button']); ?>
                    <?=Html::endForm(); ?>
            <li><div><img src="/img/pay/payments_qiwi.png"></div> QIWI с кошелька
                <?= Html::beginForm(Url::toRoute(["pay/qiwi"]), 'get', ['id' => 'paymentform']); ?>
                <?= Html::hiddenInput('sum', $sum); ?>

                <?=Html::submitButton("Выбрать", ['class' => 'button']); ?>
                <?=Html::endForm(); ?>
            </li>

            <li><div><img src="/img/pay/payments_webmoney.png"></div> WebMoney
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

                <?=Html::submitButton("Выбрать", ['class' => 'button']); ?>

                <?php echo Html::endForm(); ?>
            </li>
            <li><div><img src="/img/pay/payments_mts.png"></div> МТС
                <?= Html::beginForm("https://www.oplata.info/asp2/pay_options.asp", 'get', ['id' => 'paymentform']); ?>
                <?= Html::hiddenInput('id_d', $site->platiru_id); ?>
                <?= Html::hiddenInput('cart_uid', ""); ?>
                <?= Html::hiddenInput('ai', ""); ?>
                <?= Html::hiddenInput('ae', ""); ?>
                <?= Html::hiddenInput('failpage', "https://www.plati.com/asp/pay.asp?idd=".$site->platiru_id."&"); ?>
                <?= Html::hiddenInput('site_id', $site->id); ?>
                <?= Html::hiddenInput('user_id', \Yii::$app->getUser()->getId()); ?>
                <?= Html::hiddenInput('unit_cnt', $checks); ?>
                <?= Html::hiddenInput('curr', "MTS"); ?>
                <?= Html::hiddenInput('lang', 'ru-RU'); ?>
                <?=Html::submitButton("Выбрать", ['class' => 'button']); ?>
                <?=Html::endForm(); ?>
            </li>
            <li><div><img src="/img/pay/payments_megafon.png"></div> Мегафон <?= Html::beginForm("https://www.oplata.info/asp2/pay_options.asp", 'get', ['id' => 'paymentform']); ?>
                <?= Html::hiddenInput('id_d', 2325868); ?>
                <?= Html::hiddenInput('cart_uid', ""); ?>
                <?= Html::hiddenInput('ai', ""); ?>
                <?= Html::hiddenInput('ae', ""); ?>
                <?= Html::hiddenInput('failpage', "https://www.plati.com/asp/pay.asp?idd=2325868&"); ?>
                <?= Html::hiddenInput('site_id', $site->id); ?>
                <?= Html::hiddenInput('user_id', \Yii::$app->getUser()->getId()); ?>
                <?= Html::hiddenInput('unit_cnt', $checks); ?>
                <?= Html::hiddenInput('curr', "MGF"); ?>
                <?= Html::hiddenInput('lang', 'ru-RU'); ?>
                <?=Html::submitButton("Выбрать", ['class' => 'button']); ?>
                <?=Html::endForm(); ?></li>
            <li><div><img src="/img/pay/payments_beeline.png"></div> Билайн <?= Html::beginForm("https://www.oplata.info/asp2/pay_options.asp", 'get', ['id' => 'paymentform']); ?>
                <?= Html::hiddenInput('id_d', $site->platiru_id); ?>
                <?= Html::hiddenInput('cart_uid', ""); ?>
                <?= Html::hiddenInput('ai', ""); ?>
                <?= Html::hiddenInput('ae', ""); ?>
                <?= Html::hiddenInput('failpage', "https://www.plati.com/asp/pay.asp?idd=".$site->platiru_id."&"); ?>
                <?= Html::hiddenInput('site_id', $site->id); ?>
                <?= Html::hiddenInput('user_id', \Yii::$app->getUser()->getId()); ?>
                <?= Html::hiddenInput('unit_cnt', $checks); ?>
                <?= Html::hiddenInput('curr', "BLN"); ?>
                <?= Html::hiddenInput('lang', 'ru-RU'); ?>
                <?=Html::submitButton("Выбрать", ['class' => 'button']); ?>
                <?=Html::endForm(); ?></li>
            <li><div><img src="/img/pay/payments_sberbank.png"></div> Сбербанк <?= Html::beginForm("https://www.oplata.info/asp2/pay_options.asp", 'get', ['id' => 'paymentform']); ?>
                <?= Html::hiddenInput('id_d', $site->platiru_id); ?>
                <?= Html::hiddenInput('cart_uid', ""); ?>
                <?= Html::hiddenInput('ai', ""); ?>
                <?= Html::hiddenInput('ae', ""); ?>
                <?= Html::hiddenInput('failpage', "https://www.plati.com/asp/pay.asp?idd=".$site->platiru_id."&"); ?>
                <?= Html::hiddenInput('site_id', $site->id); ?>
                <?= Html::hiddenInput('user_id', \Yii::$app->getUser()->getId()); ?>
                <?= Html::hiddenInput('unit_cnt', $checks); ?>
                <?= Html::hiddenInput('curr', "SBR"); ?>
                <?= Html::hiddenInput('lang', 'ru-RU'); ?>
                <?=Html::submitButton("Выбрать", ['class' => 'button']); ?>
                <?=Html::endForm(); ?></li>
        </ul>

        <p class="payments-info"><img src="/img/pay/payments_info.png"> При оплате через сбербанк или со счета мобильного телефона мы разделяем с Вами комисиию от платежа. Для оплаты без комиссии рекомендуем оплатить банковской картой, Яндекс Деньгами, QIWI или через WebMoney</p>
    </div>
</div>

<?php if(\Yii::$app->devicedetect->isMobile() || \Yii::$app->devicedetect->isTablet()): ?>
<?php if(false):?><script><?php endif; ?>
    <?php ob_start(); ?>
    $(".payment-methods").find('li').each(function() {
        var self = $(this);
        var form = self.find('form');
        self.click(function() {
            form.submit();
        })
    });
    <?php $js = ob_get_contents(); ob_end_clean(); $this->registerJs($js); ?>
<?php endif; ?>
