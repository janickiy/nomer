<?php
/* @var $this \yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;
use \app\models\Site;

$site = Site::find()->where(["name" => $_SERVER["HTTP_HOST"]])->one();

$this->title = "2 проверки за репост";
?>
<div class="breadcrumbs">
    <ul class="breadcrumb">
        <?php if(\Yii::$app->devicedetect->isMobile() || \Yii::$app->devicedetect->isTablet()): ?>
            <li><?= Html::a('Главная', Url::home()) ?></li>
            <li><?= Html::a("Покупка проверок", Url::toRoute(['pay/index'])) ?></li>
            <li><?=$this->title;?></li>
        <?php else: ?>
            <li><?= Html::a('Главная', Url::home()) ?></li>
            <li><?= Html::a("Покупка проверок", Url::toRoute(['pay/index'])) ?></li>
            <li><?=$this->title;?></li>
        <?php endif; ?>
    </ul>
</div>

<div class="page-content">
    <div class="cont clfix">
        <h1><?=$this->title;?></h1>

        <?php if($hasRepost): ?>
            Вы уже получали проверки за репост
        <?php else: ?>
            <p class="payments-info"><img src="/img/pay/payments_info.png">
            Для получении 2-х бесплатных проверок вам нужно быть авторизованым в социальной сети "В контакте".
                Ваш аккаунт должен быть зарегистрирован более 2х месяцев назад и у вас должно быть более 20 друзей.
            Важно: нужно нажать именно "Рассказать друзьям", что бы запись появилась у вас на стене.
                Проверки будут начислены сразу же после репоста, но <b>репост должен провисеть на стене минимум 24 часа</b> :)</p>

            <br><br><br>

            <div id="vk_like"></div>
        <?php endif; ?>
    </div>
</div>

<?php if(false): ?><script><?php endif; ?>
<?php ob_start(); ?>
    VK.init({apiId: <?=$site->vk_id;?>, onlyWidgets: true});
    VK.Widgets.Like('vk_like', {
        type: 'full',
        pageUrl: "https://tels.gg",
        pageTitle: 'А вы знали, что можно пробить всю информацию о человек по его телефону? Всем советую',
    }, <?=\Yii::$app->getUser()->getId();?>);

    VK.Observer.subscribe("widgets.like.shared", function f()
    {
        $.getJSON("<?=Url::toRoute(["pay/check-repost"]);?>", {}, function() {
            $('#vk_like').html("Проверки зачислены!");
        });
    });
<?php $js = ob_get_contents(); ob_end_clean(); $this->registerJs($js, $this::POS_LOAD); ?>