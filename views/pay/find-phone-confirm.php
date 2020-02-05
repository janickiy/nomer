<?php
/* @var $this \yii\web\View */
/* @var $request \app\models\PhoneRequest */
/* @var $id integer */
/* @var $dataType string */
/* @var $data string|array */

use \app\components\ConfigHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$sum = 1000;

$order = new \app\models\WebmoneyOrder();
$order->sum = $sum;
$order->site_id = ConfigHelper::getInstance()->getSiteId();
$order->user_id = -$id;
$order->save();

$site = \app\models\Site::find()->where(["name" => $_SERVER["HTTP_HOST"]])->one();

$this->title = "Выбор способа оплаты";
?>

<div class="breadcrumbs">
    <ul class="breadcrumb">
        <?php if(\Yii::$app->devicedetect->isMobile() || \Yii::$app->devicedetect->isTablet()): ?>
            <li><?= Html::a('Главная', Url::home()) ?></li>
            <li><?= Html::a("Оплата поиска номера телефона", Url::toRoute(['pay/index'])) ?></li>
        <?php else: ?>
            <li><?= Html::a('Главная', Url::home()) ?></li>
            <li><?= Html::a("Оплата поиска номера телефона", Url::toRoute(['pay/index'])) ?></li>
            <li><?=$this->title;?></li>
        <?php endif; ?>
    </ul>
</div>

<div class="page-content">
    <div class="cont clfix">

        <h1>Выберите способ оплаты</h1>
        <br />
        <ul class="payment-methods">
            <li><div><img src="/img/pay/payments_card.png"></div> Кредитная карта
                <?= Html::beginForm('https://money.yandex.ru/quickpay/confirm.xml', 'post', ['id' => 'paymentform']); ?>
                <input type="hidden" value="AC" name="paymentType">
                <input type="hidden" value="<?= \Yii::$app->getUser()->id; ?>-<?=ConfigHelper::getInstance()->getSiteId();?>" name="label">
                <input type="hidden" value="<?=ConfigHelper::getInstance()->getYandexMoney();?>" name="receiver">
                <input type="hidden" value="shop" name="quickpay-form">
                <input type="hidden" value="Оплата поиска номера телефона в <?= \Yii::$app->name; ?> (#<?= $id; ?>)" name="targets">
                <input type="hidden" value="<?= Url::toRoute(['pay/find-phone-success'], 'https'); ?>"
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
                <input type="hidden" value="Оплата поиска номера телефона в <?= \Yii::$app->name; ?> (#<?= $id; ?>)" name="targets">
                <input type="hidden" value="<?= Url::toRoute(['pay/find-phone-success'], 'https'); ?>"
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
                Html::hiddenInput('LMI_SUCCESS_URL', Url::toRoute(["pay/find-phone-success"], 'https')),
                Html::hiddenInput('LMI_SIM_MODE', 1);
                ?>

                <?=Html::submitButton("Выбрать", ['class' => 'button']); ?>

                <?php echo Html::endForm(); ?>
            </li>
        </ul>

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
