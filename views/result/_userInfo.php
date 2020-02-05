<?php
/* @var $this \yii\web\View */
use yii\helpers\Url;

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

<div class="userInfo">
    <div class="tarif">Тариф - <span><?= $plan; ?></span>
        <?php if ($is_guest): ?>
            <a href="" class="link">Зарегистрироваться</a>
        <?php else: ?>
            <?php if (!$is_vip): ?>
                <?php if (!$is_test): ?>
                    <a href="<?= Url::toRoute(["try/index"]); ?>" class="link">Попробовать</a>
                <?php else: ?>
                    <a href="<?= Url::toRoute(["buy/index"]); ?>" class="link">Купить пакет</a>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php if (!$is_guest): ?>
        <div class="balans">Баланс - <span><?= \Yii::$app->formatter->asCurrency($user->balance, "RUB"); ?></span><a
                href="<?= Url::toRoute(["pay/index"]); ?>" class="link">Пополнить счет</a></div>
    <?php endif; ?>
    <a href="" class="sAppStore"></a>
    <a href="" class="sGp"></a>
</div>
