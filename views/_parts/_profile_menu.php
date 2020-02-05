<?php
/* @var $this \yii\web\View */
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $user \app\models\User */
$user = \Yii::$app->getUser()->getIdentity();

$is_guest = \Yii::$app->user->isGuest;
$is_test = false;
$is_vip = false;

$plan = "Гостевой";

if (!$is_guest) {
    /* @var $user \app\models\User */
    $user = \Yii::$app->user->getIdentity();
    if ($user->is_vip) {
        $is_vip = true;
    }

    if ($is_test) {
        $plan = "Тестовый";
        if ($user->plan) {
            $plan = "Предоплаченный";
        }
    } else {
        switch ($user->plan) {
            case 0:
                $plan = "Ограниченный";
                break;
            case 1:
                $plan = "Предоплаченный";
                break;
        }
    }
}

if ($is_vip) {
    $plan = "VIP";
}
?>
<?php if(isset($_SERVER["is_mobile"]) && $_SERVER["is_mobile"] == 1): ?>
    <div id="mySidenav" class="sidenav">
        <a href="javascript:void(0)" class="closebtn" onclick="$(this).parent().width(0)">&times;</a>
        <ul>
            <li>Тариф: <?=$plan;?></li>
            <li>Баланс: <?=\Yii::$app->formatter->asCurrency($user->balance, "RUB");?></li>
            <li><a href="/history">История поисков</a></li>
            <li><a href="/contacts" class="user-menu__item">Контакты</a></li>
            <li><a href="/settings">Настройки профиля</a></li>
            <li><a href="/logout">Выйти</a></li>
        </ul>
    </div>
<?php else: ?>
    <div class="myProfileMenu">
        <div class="mLine mLogin">Логин: <span><?=$user->email;?></span></div>
        <div class="mLine mBalans">
            <!--<span class="mTarif">Тариф: <strong>vip</strong></span>-->
            <a href="<?=Url::toRoute(["pay/index"]);?>">Проверок: <strong><?=\Yii::$app->params["payModel"]?Yii::$app->formatter->asInteger($user->checks):"неогр.";?></strong></a>
        </div>
        <div class="mLine mHistory"><?=Html::a("История поисков", ["history/index"]);?></div>
        <div class="mLine mConstacts"><?=Html::a("Ваши контакты", ["contacts/index"]);?></div>
        <div class="mLine mProfile"><?=Html::a("Настройки профиля", ["settings/index"]);?></div>
        <div class="mLine mExit"><?=Html::a("Выйти", ["site/logout"]);?></div>
        <?php /*
        <div class="mLine mShps">
            <a href=""><img src="img/l2.png" alt=""></a>
            <a href=""><img src="img/l1.png" alt=""></a>
            <a class="mBot" href="">@bot_zapalil</a>
        </div>
        */ ?>
    </div>
<?php endif; ?>