<?php

/* @var $this \yii\web\View */

use app\components\ConfigHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$price = \Yii::$app->request->get('price');
$comment = 'block-' . Yii::$app->session->get('lastBlockPhone', null);

$getPhoneImage = function($phone) {
    $image = new Imagick(realpath('../web/img/qiwi/qiwi_4.jpeg'));

    $r = new ImagickDraw();
    $r->setFillColor('white');
    $r->rectangle(130, 110, 300, 130);

    $image->drawImage($r);

    $text = new ImagickDraw();
    $text->setFontSize(15);
    $text->setFillColor('black');
    $text->setFontWeight(555);

    $image->annotateImage($text, 140, 120, 0, $phone);

    return base64_encode($image);
};

$getCommentImage = function($comment) {
    $image = new Imagick(realpath('../web/img/qiwi/qiwi_5.jpeg'));

    $text = new ImagickDraw();
    $text->setFontSize(15);
    $text->setFillColor('black');
    $text->setFontWeight(555);

    $image->annotateImage($text, 115, 100, 0, $comment);

    return base64_encode($image);
};

$this->title = "Пополнение через QIWI";

$this->registerCss('
@media print {
    header {
      display: none;
    }
    
    .breadcrumbs {
      display: none !important;
    }
    
    .page-content {
      padding-top: 5px;
      background: none;
    }
    
    .cont.clfix {
      max-width: 700%;
    }
    
    #form {
      display: none;
    }
    
    footer {
      display: none;
    }
}');

?>
<div class="breadcrumbs">
    <ul class="breadcrumb">
        <?php if (Yii::$app->devicedetect->isMobile() || \Yii::$app->devicedetect->isTablet()): ?>
            <li><?= Html::a('Главная', Url::home()) ?></li>
            <li><?= Html::a('VIP исключение номера из базы', Url::toRoute(['block/pay'])) ?></li>
        <?php else: ?>
            <li><?= Html::a('Главная', Url::home()) ?></li>
            <li><?= Html::a('VIP исключение номера из базы', Url::toRoute(['block/pay'])) ?></li>
            <li>Пополнение через QIWI</li>
        <?php endif; ?>
    </ul>
</div>

<div class="page-content">
    <div class="cont clfix">
        <h1>Оплата через QIWI</h1>
        <p class="qiwi-descr">Для VIP удаления номера из базы необходимо<br>отправить <span><?= $price; ?> руб</span>
            на QIWI-кошелек</p>

        <div class="qiwi">
            <input class="qiwi" value="+<?= ConfigHelper::getInstance()->getQiwiPhone(); ?>" type="text">

            <p class="qiwi-comment">С комментарием про оплату:</p>
            <input class="qiwi" type="text" value="<?=$comment;?>">

            <p class="qiwi-comment">Розыск платежа:</p>
            <input id="check-value" class="qiwi" type="text" placeholder="Введите номер телефона или ID транзакции">
            <div class="qiwi-buttons">
                <button class="button" onclick="print();">Распечатать инструкцию</button>
                <button class="button" onclick="check();">Розыск платёжа</button>
            </div>
            <p class="alert"></p>
            <p class="danger">Будьте внимательны! Пополнять нужно QIWI кошелек, а не мобильный телефон!</p>
        </div>
        <h1>Инструкция пополнения через QIWI</h1>
        <?php /*
        <p><input id="check-value" type="text" placeholder="Номер телефона или ID транзакции"></p>
        <p>При пополнении на сумму более 500 рублей – комиссия 0%, до 500 рублей включительно – 3% по всей России.</p>
        */ ?>

        <div class="qiwi-steps">
            <div class="qiwi-line"></div>
            <ul class="qiwi-container">
                <li>
                    <div class="qiwi-step-icon">1</div>
                    <div class="qiwi-steps-content">
                        <img src="/img/qiwi/qiwi_1.jpeg"/>
                    </div>
                    <div class="qiwi-steps-text">
                        <b>Шаг 1</b>
                        <p>Нажмите "VISA QIWI WALLET"</p>
                    </div>
                </li>
                <li>
                    <div class="qiwi-step-icon">2</div>
                    <div class="qiwi-steps-content">
                        <img src="/img/qiwi/qiwi_2.jpeg"/>
                    </div>
                    <div class="qiwi-steps-text">
                        <b>Шаг 2</b>
                        <p>Нажмите "Пополнить кошелек"</p>
                    </div>
                </li>
                <li>
                    <div class="qiwi-step-icon">3</div>
                    <div class="qiwi-steps-content">
                        <img src="/img/qiwi/qiwi_3.jpeg"/>
                    </div>
                    <div class="qiwi-steps-text">
                        <b>Шаг 3</b>
                        <p>Введите номер телефона на который зарегистрирован Visa QIWI Wallet и нажмите кнопку "Далее"</p>
                    </div>
                </li>
                <li>
                    <div class="qiwi-step-icon">4</div>
                    <div class="qiwi-steps-content">
                        <img src="data:image/png;base64,<?= $getPhoneImage(preg_replace('/(\d)(\d{3})(\d{3})(\d{2})(\d{2})/', '+$1($2)$3-$4-$5', ConfigHelper::getInstance()->getQiwiPhone())) ?>"/>"/>
                    </div>
                    <div class="qiwi-steps-text">
                        <b>Шаг 4</b>
                        <p>Подтвердите правильность введенного номера</p>
                    </div>
                </li>
                <li>
                    <div class="qiwi-step-icon">5</div>
                    <div class="qiwi-steps-content">
                        <img src="data:image/png;base64,<?= $getCommentImage($comment) ?>"/>
                    </div>
                    <div class="qiwi-steps-text">
                        <b>Шаг 5</b>
                        <p>Введите комментарий</p>
                    </div>
                </li>
                <li>
                    <div class="qiwi-step-icon">6</div>
                    <div class="qiwi-steps-content">
                        <img src="/img/qiwi/qiwi_6.jpeg"/>
                    </div>
                    <div class="qiwi-steps-text">
                        <b>Шаг 6</b>
                        <p>Внесите наличные в купюроприемник. После нажатия кнопки "Далее", внесенная сумма моментально поступит
                            на счет</p>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>

<script>
    function check() {
        var value = document.querySelector('#check-value').value;

        if (!value) return alert('Не указан номер телефона/ID транзакции');

        $.getJSON('<?= Url::toRoute(['pay/qiwi-check']) ?>?value=' + encodeURIComponent(value), function (response) {
            switch (response.code) {
                case 0:
                    $('.alert').html('Платёж не найден :(').show();
                    break;
                case 1:
                    $('.alert').html('Платёж успешно проведен, проверки зачислены.').show();
                    break;
                case 2:
                    $('.alert').html('Вероятно, платёж был ошибочно зачислен другому пользователю. Пришлите чек на support@nomer.io').show();
                    break;
                case 3:
                    $('.alert').html('Платёж не зачислился. Вероятно, вы забыли указать комментарий. Пришлите чек на support@nomer.io').show();
                    break;
                default:
                    $('.alert').html('Произошла ошибка.').show();
            }
        });
    }
</script>
