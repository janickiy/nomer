<?php

/* @var $this \yii\web\View */
/* @var $content string */

use alexandernst\devicedetect\DeviceDetect;
use app\models\RemindForm;
use app\models\SigninForm;
use app\models\SignupForm;
use app\models\UserContact;
use yii\helpers\Html;
use app\assets\AppAsset;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $deviceDetect DeviceDetect */
//$deviceDetect = Yii::$app->devicedetect;


AppAsset::register($this);

/* @var $identity \app\models\User */
$identity = \Yii::$app->getUser()->getIdentity();

$is_vip = false;
$is_test = false;
$is_guest = \Yii::$app->user->isGuest;
$is_admin = false;

if($identity && $identity->is_vip) {
    $is_vip = true;
}

if($identity && $identity->is_admin) {
    $is_admin = true;
}

$plan = "Гостевой";

$fingerprint = false;
$ec = false;

if (!$is_guest) {
    /* @var $user \app\models\User */
    $user = \Yii::$app->user->getIdentity();
    if ($user->is_vip) {
        $is_vip = true;
    }

    if($user->checks > 0 || $user->balance >= \Yii::$app->params["cost"]) {
        $plan = "Стандартный";
    } else {
        $plan = "Ограниченный";
    }

    if($identity->is_test) {
        $ip = \app\models\UserIp::find()->where(["user_id" => $user->id, "ip" => \Yii::$app->request->getUserIP()])->one();
        $fp = \app\models\UserFingerprint::find()->where(["user_id" => $user->id, "ip" => \Yii::$app->request->getUserIP()])->one();
        if(!$ip || !$fp) $fingerprint = true;
        $ec = true;
    }
}

if ($is_vip) {
    $plan = "VIP";
}

$this->registerJs("NomerIoApp.init(".($is_admin?1:0).", '".($is_guest?null:md5("nomerio-".\Yii::$app->getUser()->getId()))."');", \yii\web\View::POS_READY, "init");

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <!--<meta name="viewport" content="width=device-width, initial-scale=1">-->
    <!--<meta name="viewport" content="width=device-width">-->
    <meta name="interkassa-verification" content="713de7746e2f4bd607a6a52ea0094fc0" />
    <meta name="interkassa-verification" content="d8f1fd383d44f7ce6f482386abd8af2c" />
    <!--<meta name="viewport" content="width=device-width">-->
    <meta name="okpay-verification" content="08c244c5-bc15-4db0-a7e3-6f9c9989e6fe" />

    <!--<meta name="viewport" content="width=device-width">-->
    <meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <link rel="icon" type="image/png" href="/favicon.png" />
</head>
<body>
<?php if (YII_ENV != 'dev'): ?>
    <script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-96815159-1', 'auto');
    ga('send', 'pageview');
    </script>
<!-- Yandex.Metrika counter -->

<script type="text/javascript">
    (function (d, w, c) {
        (w[c] = w[c] || []).push(function() {
            try {
                w.yaCounter43968354 = new Ya.Metrika({
                    id:43968354,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true
                });
            } catch(e) { }
        });
        var n = d.getElementsByTagName("script")[0],
            s = d.createElement("script"),
            f = function () { n.parentNode.insertBefore(s, n); };
        s.type = "text/javascript";
        s.async = true;
        s.src = "https://mc.yandex.ru/metrika/watch.js";
        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f, false);
        } else { f(); }
    })(document, window, "yandex_metrika_callbacks");
</script>

<noscript><div><img src="https://mc.yandex.ru/watch/43968354" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
<?php endif; ?>
<?php $this->beginBody() ?>

<header <?=\Yii::$app->getUser()->isGuest?"":"class='singIn'";?>>
    <div class="cont">
        <ul class="header__menu">
            <li class="logo"><a href="<?=Url::home();?>" class="logo"><?=\Yii::$app->name;?></a></li>
            <?php if(\Yii::$app->name != 'wcaller.com' && \Yii::$app->name != 'wcaller.ru'): ?>
            <?php if(\Yii::$app->getUser()->isGuest): ?>
                <li class="userMenu">
                    <a href="#signin" class="signinButton __mobile"></a>
                    <a href="#menu" class="menuButton __mobile"></a>
                    <span class="__desktop"><a href="#signup" class="reg">Регистрация</a> / <a href="#signin" class="enter">Вход</a></span>
                </li>
            <?php else: ?>
            <li class="balance __desktop">
                Проверок доступно: <b><?=\Yii::$app->params["payModel"]?$user->checks:"неогр.";?></b> <a href="<?=Url::toRoute(["pay/index"]);?>">Купить</a>
            </li>
            <li class="profileMenu">
                <ul class="profile">
                    <li class="profileBox">
                        <?php
                        $tickets = \app\models\Ticket::find()->where(["user_id" => \Yii::$app->getUser()->id])->all();
                        $tIds = \yii\helpers\ArrayHelper::getColumn($tickets, "id");
                        $comments = \app\models\TicketComment::find()->where(["ticket_id" => $tIds])->andWhere(["tm_read" => null])->count();?>
                        <a class="myProfile __mobile" href=""></a>
                        <a class="myProfile __desktop" href="">Мой профиль <?=$comments?"<span style='color: red;'>(".$comments.")</span>":"";?></a>

                        <div class="myProfileMenu">
                            <span class="close"></span>
                            <div class="mLine mLogin">Логин: <span><?=$user->email;?></span></div>
                            <div class="mLine mBalans">
                                <!--<span class="mTarif">Тариф: <strong>vip</strong></span>-->
                                <a href="<?=Url::toRoute(["pay/index"]);?>">Проверок: <strong><?=\Yii::$app->params["payModel"]?Yii::$app->formatter->asInteger($user->checks):"неогр.";?></strong><?=$user->balance>0?". Баланс: <strong>".\Yii::$app->formatter->asCurrency($user->balance, "RUB")."</strong>":"";?></a>
                            </div>
                            <div class="mLine mHistory"><?=Html::a("История поисков", ["history/index"]);?></div>
                            <div class="mLine mConstacts"><?=Html::a("Реф. программа", ["referrals/index"]);?></div>
                            <div class="mLine mConstacts"><?=Html::a("Тикет-система".($comments?"<span style='color: red;'> (".$comments.")</span>":""), ["feedback/index"]);?></div>
                            <?php if(UserContact::find()->where(["user_id" => $identity->id])->count(1)): ?>
                                <div class="mLine mConstacts"><?=Html::a("Ваши контакты", ["contacts/index"]);?></div>
                            <?php endif; ?>
                            <div class="mLine mProfile"><?=Html::a("Настройки профиля", ["settings/index"]);?></div>
                            <div class="mLine mExit"><?=Html::a("Выйти", ["site/logout"]);?></div>

                            <div class="mLine mShps">
                                <a href="https://play.google.com/store/apps/details?id=com.nomergg.app&utm_medium=special&utm_source=<?=\Yii::$app->name;?>&utm_campaign=site&utm_content=profile"><img src="/img/l2.png" alt="Android App"></a>
                                <a href="https://itunes.apple.com/RU/app/id1214336721?utm_medium=special&utm_source=<?=\Yii::$app->name;?>&utm_campaign=site&utm_content=footer"><img src="/img/l1.png" alt="iOS App"></a>
                                <?php /*
                            <a href="<?=Url::toRoute(["apps/index"]);?>"><img src="img/l1.png" alt=""></a>
                            */ ?>
                                <a class="mBot" href="<?=Url::toRoute(["apps/index"]);?>">@nomergg_bot</a>
                            </div>

                        </div>
                    </li>
                </ul>
            </li>
            <?php endif; ?>
            <?php endif; ?>
        </ul>
    </div>
</header>

<section id="content">
    <?= $content ?>
</section>

<div class="fakeFooter"></div>

<footer>

    <?php if(\Yii::$app->devicedetect->isMobile() || \Yii::$app->devicedetect->isTablet()): ?>
    <div class="cont" style="margin-top: 20px; text-align: center">
        <a href="https://play.google.com/store/apps/details?id=com.nomergg.app&utm_medium=special&utm_source=<?=\Yii::$app->name;?>&utm_campaign=site&utm_content=footer"><img src="/img/l2.png" alt="Android App"></a>
        <a href="https://itunes.apple.com/RU/app/id1214336721?utm_medium=special&utm_source=<?=\Yii::$app->name;?>&utm_campaign=site&utm_content=footer"><img src="/img/l1.png" alt="iOS App"></a>
    </div>
    <?php else: ?>
        <div class="cont">
            <table style="width: 100%">
                <tr>
                    <td style="text-align: left;">
                    <a href="<?=Url::home();?>" class="logo"><?=\Yii::$app->name;?></a>
                    <span class="footer__menu">
                    <a href="<?= Url::toRoute(['block/index']) ?>">Удаление номера</a>
                    /
                    <a href="<?= Url::toRoute(['feedback/index']) ?>">Обратная связь</a>
                    </span>
                    </td>
                    <td style="text-align: right;">
            <a href="https://play.google.com/store/apps/details?id=com.nomergg.app&utm_medium=special&utm_source=<?=\Yii::$app->name;?>&utm_campaign=site&utm_content=footer"><img src="/img/l2.png" alt="Android App"></a>
            <a href="https://itunes.apple.com/RU/app/id1214336721?utm_medium=special&utm_source=<?=\Yii::$app->name;?>&utm_campaign=site&utm_content=footer"><img src="/img/l1.png" alt="iOS App"></a>
                    </td>
                </tr>
            </table>
        </div>
    <?php endif; ?>
</footer>
<div class="signin-modal-overlay" id="menu">
    <div class="modal-close"><a href="#"></a></div>
    <div class="modal">
        <ul>
            <li><a href="<?=Url::toRoute(["/", "#" => "phone"]);?>">Поиск по номеру телефона</a></li>
            <li><a href="<?=Url::toRoute(["/", "#" => "profile"]);?>">Определение номера по профилю в facebook, VK, Instagram или Email</a></li>
            <li><a href="<?=Url::toRoute(["block/index"]);?>">Удаление номера</a></li>
            <li><a href="<?=Url::toRoute(["feedback/index"]);?>">Обратная связь</a></li>
            <li class="apps">
                <a href="https://play.google.com/store/apps/details?id=com.nomergg.app&utm_medium=special&utm_source=<?=\Yii::$app->name;?>&utm_campaign=site&utm_content=footer"><img src="/img/l2.png" alt="Android App"></a>
                <a href="https://itunes.apple.com/RU/app/id1214336721?utm_medium=special&utm_source=<?=\Yii::$app->name;?>&utm_campaign=site&utm_content=footer"><img src="/img/l1.png" alt="iOS App"></a>
            </li>
        </ul>
    </div>
</div>
<?php if(\Yii::$app->getUser()->isGuest): ?>
    <?php
    $signInModel = new SigninForm();
    $signUpModel = new SignupForm();
    $remindModel = new RemindForm();
    ?>
<div class="signin-modal-overlay" id="signin">
    <div class="modal-close"><a href="#"></a></div>
    <div class="modal">
        <div class="modal-title">Вход</div>
        <div class="modal-content">
            <div class="auth-content">
                <p class="text-center auth-text auth-via-socials">Через социальные сети</p>
                <a href="<?=Url::toRoute(['site/auth', 'authclient' => 'vk']);?>" class="auth-link auth-button --vkontakte"><span></span>ВКонтакте</a>
                <a href="<?=Url::toRoute(['site/auth', 'authclient' => 'facebook']);?>" class="auth-link auth-button --facebook"><span></span>Facebook</a>
                <a href="<?=Url::toRoute(['site/auth', 'authclient' => 'google']);?>" class="auth-link auth-button --google"><span></span>Google+</a>
                <div class="auth-text auth-text-or">
                    <span>или</span>
                </div>
                <?php $signInForm = ActiveForm::begin([
                    'enableAjaxValidation' => true,
                    'action' => Url::toRoute(["site/signin"])
                ]); ?>
                <div class="auth-fields">
                    <?=$signInForm->field($signInModel, 'email', [
                        'template' => '{input}{error}'
                    ])->textInput([
                        "placeholder" => $signInModel->getAttributeLabel("email")
                    ]);?>
                    <?=$signInForm->field($signInModel, 'password', [
                        'template' => '{input}{error}'
                    ])->passwordInput([
                        "placeholder" => $signInModel->getAttributeLabel("password")
                    ]);?>
                    <?=Html::submitButton("Войти", ["class" => 'signin']);?>
                </div>
                <?php ActiveForm::end(); ?>
                <p class="text-right auth-logform-bottom-links auth-bottom-links">
                    <a href="#signup" class="pull-left auth-text-btn">Регистрация</a>
                    <a href="#remind" class="auth-text-btn">Забыли пароль?</a>
                </p>
            </div>
        </div>
    </div>
</div>
<div class="signin-modal-overlay" id="signup">
    <div class="modal-close"><a href="#"></a></div>
    <div class="modal">
        <div class="modal-title">Регистрация</div>
        <div class="modal-content">
            <div class="auth-content">
                <p class="text-center auth-text auth-via-socials">Через социальные сети</p>
                <a href="<?=Url::toRoute(['site/auth', 'authclient' => 'vk']);?>" class="auth-link auth-button --vkontakte"><span></span>ВКонтакте</a>
                <a href="<?=Url::toRoute(['site/auth', 'authclient' => 'facebook']);?>" class="auth-link auth-button --facebook"><span></span>Facebook</a>
                <a href="<?=Url::toRoute(['site/auth', 'authclient' => 'google']);?>" class="auth-link auth-button --google"><span></span>Google+</a>
                <div class="auth-text auth-text-or">
                    <span>или</span>
                </div>
                <?php $signUpForm = ActiveForm::begin([
                    'enableAjaxValidation' => true,
                    'action' => Url::toRoute(["site/signup"])
                ]); ?>
                <div class="auth-fields">
                    <?=$signUpForm->field($signUpModel, 'email', [
                        'template' => '{input}{error}'
                    ])->textInput([
                        "placeholder" => $signUpModel->getAttributeLabel("email")
                    ]);?>
                    <?=$signUpForm->field($signUpModel, 'password', [
                        'template' => '{input}{error}'
                    ])->passwordInput([
                        "placeholder" => $signUpModel->getAttributeLabel("password")
                    ]);?>
                    <?=Html::submitButton("Зарегистрироваться", ["class" => 'signin']);?>
                </div>
                <p class="auth-text-notes">Регистрируясь, вы подтверждаете, что прочитали и согласны с <?=Html::a("пользовательским соглашением", "/offerta.pdf");?>.</p>
                <?php ActiveForm::end(); ?>
                <p class="auth-text-notes--last text-center auth-bottom-links">
                    <a href="#signin" class="auth-text-btn">Уже есть аккаунт?</a>
                </p>
            </div>
        </div>
    </div>
</div>
<div class="signin-modal-overlay" id="remind">
    <div class="modal-close"><a href="#"></a></div>
    <div class="modal">
        <div class="modal-title">Восстановление доступа</div>
        <div class="modal-content">
            <div class="auth-content">
                <p class="text-center auth-text auth-via-socials">Пожалуйста, укажите e-mail, который Вы использовали для входа на сайт.</p>
                <?php $remindForm = ActiveForm::begin([
                    'enableAjaxValidation' => false,
                    'action' => Url::toRoute(["site/remind"])
                ]); ?>
                <div class="auth-fields">
                    <?=$remindForm->field($remindModel, 'email', [
                        'template' => '{input}{error}'
                    ])->textInput([
                        "placeholder" => $remindModel->getAttributeLabel("email")
                    ]);?>
                    <?=Html::submitButton("Восстановить", ["class" => 'signin']);?>
                </div>
                <?php ActiveForm::end(); ?>
                <p class="text-right auth-logform-bottom-links auth-bottom-links">
                    <a href="#signup" class="pull-left auth-text-btn">Регистрация</a>
                    <a href="#signin" class="auth-text-btn">Вход</a>
                </p>
                <p class="text-center auth-text-notes auth-text-notes--last">Ничего не вспоминается?<br><a href="<?=Url::toRoute(["site/contacts"]);?>">Напишите нам</a></p>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>

<?php if(\Yii::$app->devicedetect->is('iOS')):?>
<div class="mobileapp">
    <div class="application-plate__close-button-wrap">✕</div>
    <div class="application-plate__link">
        <div class="application-plate__call-to-action --ios">
            <i class="application-plate__icon"></i>
            <div class="application-plate__text">
                <div class="application-plate__text__inner-wrap">
                    <div>Установите приложение <?=\Yii::$app->name;?> в&nbsp;<span>Apple Store</span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="application-plate__install-button">установить</div>
</div>
<?php else: ?>
<div class="mobileapp">
    <div class="application-plate__close-button-wrap">✕</div>
    <div class="application-plate__link">
        <div class="application-plate__call-to-action --gp">
            <i class="application-plate__icon"></i>
            <div class="application-plate__text">
                <div class="application-plate__text__inner-wrap">
                    <div>Установите приложение <?=\Yii::$app->name;?> в&nbsp;<span>Google Play</span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="application-plate__install-button">установить</div>
</div>
<?php endif;?>


<?php if(false):?><script><?php endif; ?>

    <?php ob_start(); ?>

    function getCookie(name) {
        var matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }

    <?php if(isset($signUpForm)): ?>
    $('form#<?=$signUpForm->getId();?>').on('beforeSubmit', function () {
        ga('send', { 'hitType': 'pageview', 'page': '/signup/submitted/', 'title': 'Register Submitted' });
        ga('send', 'event', 'button', 'click', 'register-submit');
    });
    <?php endif; ?>

    var ua = navigator.userAgent.toLowerCase();

    var isAndroid = ua.indexOf("android") > -1; //&& ua.indexOf("mobile");
    var iOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;

    var isInstallApp = getCookie("installApp");
    var isInstallTry = getCookie("installAppTry");
    if(typeof isInstallTry === "undefined") {
        isInstallTry = 1;
    } else {
        isInstallTry++;
    }
    var date = new Date(new Date().getTime() +  90 * 24 * 60 * 60 * 1000);
    document.cookie = "installAppTry="+isInstallTry+"; path=/; expires=" + date.toUTCString();
    if((isAndroid) && ( typeof isInstallApp === "undefined")) {
        setTimeout(function () {
            $('.mobileapp').addClass('move-in');
        }, 4);
    }

    var ddate;
    switch (isInstallApp) {
        case 1: ddate = new Date(new Date().getTime() +  60 * 60 * 1000); break;
        case 2: ddate = new Date(new Date().getTime() + 24 * 60 * 60 * 1000); break;
        case 3: ddate = new Date(new Date().getTime() + 365 * 24 * 60 * 60 * 1000); break;
        default: ddate = new Date(new Date().getTime() +  60 * 60 * 1000);
    }

    $('.application-plate__install-button').click(function() {
        document.cookie = "installApp=1; path=/; expires=" + ddate.toUTCString();
        $('.mobileapp').removeClass('move-in');
        if(isAndroid) {
            window.open("https://play.google.com/store/apps/details?id=com.nomergg.app&utm_medium=special&utm_source=<?=\Yii::$app->name;?>&utm_campaign=site&utm_content=popup");
        }
        if(iOS) {
            window.open("https://itunes.apple.com/RU/app/id1214336721?utm_medium=special&utm_source=<?=\Yii::$app->name;?>&utm_campaign=site&utm_content=popup");
        }
    });

    $('.application-plate__close-button-wrap').click(function() {
        document.cookie = "installApp=1; path=/; expires=" + ddate.toUTCString();

        $('.mobileapp').removeClass('move-in');
    });

    <?php $js = ob_get_contents(); ob_end_clean(); if(\Yii::$app->getUser()->isGuest) {$this->registerJs($js);} ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>