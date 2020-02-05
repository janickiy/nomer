<?php

/* @var $this \yii\web\View */

/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Telegram;
use app\models\Token;
use app\modules\admin\assets\ThemeAsset;

ThemeAsset::register($this);

$this->registerCss('.badge-menu { margin-left: 10px; }');

?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<!--[if IE 8]>
<html lang="<?= Yii::$app->language ?>" class="ie8"> <![endif]-->
<!--[if IE 9]>
<html lang="<?= Yii::$app->language ?>" class="ie9"> <![endif]-->
<!--[if !IE]><!-->
<html lang="<?= Yii::$app->language ?>"> <!--<![endif]-->
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <?= Html::csrfMetaTags() ?>
    <title><?= Yii::$app->name ?> admin - <?= Html::encode($this->title) ?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta content="nomer.io Admin" name="description"/>
    <meta content="nomer.io" name="author"/>

    <link rel="shortcut icon" href="/favicon.ico"/>

    <?php $this->head(); ?>
</head>
<body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white">
<?php $this->beginBody(); ?>
<div class="page-wrapper">
    <!-- BEGIN HEADER -->
    <div class="page-header navbar navbar-fixed-top">
        <!-- BEGIN HEADER INNER -->
        <div class="page-header-inner ">
            <!-- BEGIN LOGO -->
            <div class="page-logo">
                <div class="menu-toggler sidebar-toggler">
                    <span></span>
                </div>
            </div>
            <!-- END LOGO -->
            <!-- BEGIN RESPONSIVE MENU TOGGLER -->
            <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse"
               data-target=".navbar-collapse">
                <span></span>
            </a>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="page-container">

        <!-- BEGIN SIDEBAR -->
        <div class="page-sidebar-wrapper">
            <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
            <!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
            <div class="page-sidebar navbar-collapse collapse">
                <!-- BEGIN SIDEBAR MENU -->
                <ul class="page-sidebar-menu">
                    <?php if (\Yii::$app->user->id != 247): ?>
                        <li class="start <?= Yii::$app->controller->id == 'dashboard' ? 'active' : '' ?>">
                            <a href="<?= Url::toRoute(['dashboard/index']) ?>">
                                <i class="icon-home"></i>
                                <span class="title">Dashboard</span>
                            </a>
                        </li>
                        <li class="<?= Yii::$app->controller->id == 'users' ? 'active' : '' ?>">
                            <a href="<?= Url::toRoute(['users/index']) ?>">
                                <i class="icon-users"></i>
                                <span class="title">Пользователи</span>
                            </a>
                        </li>
                        <li class="<?= Yii::$app->controller->id == 'referrals' ? 'active' : '' ?>">
                            <a href="<?= Url::toRoute(['referrals/index']) ?>">
                                <i class="icon-user-follow"></i>
                                <span class="title">Реферальная система</span>
                            </a>
                        </li>
                        <li class="<?= Yii::$app->controller->id == 'history' ? 'active' : '' ?>">
                            <a href="<?= Url::toRoute(['history/index']) ?>">
                                <i class="icon-list"></i>
                                <span class="title">История поиска</span>
                                <span class="selected"></span>
                            </a>
                        </li>
                        <li class="<?= Yii::$app->controller->id == 'tickets' ? 'active' : '' ?>">
                            <?php
                            $tickets = \app\models\Ticket::find()->where(["status" => [0, 1], "is_deleted" => 0])->count();
                            ?>
                            <a href="<?= Url::toRoute(['tickets/index']) ?>">
                                <i class="icon-speech "></i>
                                <span class="title">Система тикетов</span>
                                <?php if ($tickets): ?>
                                    <span class="badge badge-danger"><?= $tickets; ?></span>
                                <?php endif; ?>
                                <span class="selected"></span>
                            </a>
                        </li>
                        <li class="<?= Yii::$app->controller->id == 'stats' && Yii::$app->controller->action->id == 'index' ? 'active' : '' ?>">
                            <a href="<?= Url::toRoute(['stats/index']) ?>">
                                <i class="icon-bar-chart"></i>
                                <span class="title">Статистика</span>
                                <span class="selected"></span>
                            </a>
                        </li>
                        <li class="<?= Yii::$app->controller->id == 'payments' ? 'active' : '' ?>">
                            <a href="<?= Url::toRoute(['payments/index']) ?>">
                                <i class="icon-credit-card"></i>
                                <span class="title">Платежи</span>
                                <span class="selected"></span>
                            </a>
                        </li>
                        <li class="<?= Yii::$app->controller->id == 'subscriptions' ? 'active' : '' ?>">
                            <a href="<?= Url::toRoute(['subscriptions/index']) ?>">
                                <i class="icon-credit-card"></i>
                                <span class="title">Подписки</span>
                                <span class="selected"></span>
                            </a>
                        </li>
                        <li class="<?= Yii::$app->controller->id == 'checkouts' ? 'active' : '' ?>">
                            <a href="<?= Url::toRoute(['checkouts/index']) ?>">
                                <i class="icon-credit-card"></i>
                                <span class="title">Выплаты</span>
                                <span class="selected"></span>
                            </a>
                        </li>
                        <li class="<?= Yii::$app->controller->id == 'sites' ? 'active' : '' ?>">
                            <a href="<?= Url::toRoute(['sites/index']) ?>">
                                <i class="icon-globe"></i>
                                <span class="title">Сайты</span>
                                <span class="selected"></span>
                            </a>
                        </li>
                        <li class="<?= Yii::$app->controller->id == 'notify' ? 'active' : '' ?>">
                            <a href="<?= Url::toRoute(['notify/index']) ?>">
                                <i class="icon-energy"></i>
                                <span class="title">Уведомления</span>
                                <span class="selected"></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="<?= Yii::$app->controller->id == 'wallets' ? 'active' : '' ?>">
                        <a href="<?= Url::toRoute(['wallets/index']) ?>">
                            <i class="icon-wallet"></i>
                            <span class="title">Кошельки</span>
                            <span class="selected"></span>
                        </a>
                    </li>
                    <?php if (\Yii::$app->getUser()->id == 1): ?>
                        <li class="<?= Yii::$app->controller->id == 'tokens' ? 'active' : '' ?>">
                            <a href="<?= Url::toRoute(['tokens/index']) ?>">
                                <i class="icon-key"></i>
                                <span class="title">Токены<?php if ($count = Token::find()->where(['status' => Token::STATUS_INACTIVE])->count()) echo '<span class="badge badge-menu badge-danger">' . $count . '</span>' ?></span>
                                <span class="selected"></span>
                            </a>
                        </li>
                        <li class="<?= Yii::$app->controller->id == 'accounts' && Yii::$app->controller->action->id == 'telegram' ? 'active' : '' ?>">
                            <a href="<?= Url::toRoute(['accounts/telegram']) ?>">
                                <i class="icon-paper-plane"></i>
                                <span class="title">Telegram<?php if ($count = Telegram::find()->where(['status' => [Telegram::STATUS_INACTIVE, Telegram::STATUS_UNAVAILABLE]])->count()) echo '<span class="badge badge-menu badge-danger">' . $count . '</span>' ?></span>
                                <span class="selected"></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (\Yii::$app->user->id != 247): ?>
                        <li class="<?= Yii::$app->controller->id == 'reposts' ? 'active' : '' ?>">
                            <a href="<?= Url::toRoute(['reposts/index']) ?>">
                                <i class="icon-share"></i>
                                <span class="title">Репосты</span>
                                <span class="selected"></span>
                            </a>
                        </li>

                        <li class="<?= Yii::$app->controller->id == 'retargeting' ? 'active' : '' ?>">
                            <a href="<?= Url::toRoute(['retargeting/index']) ?>">
                                <i class="icon-bar-chart"></i>
                                <span class="title">Ретаргетинг</span>
                                <span class="selected"></span>
                            </a>
                        </li>

                        <li class="<?= Yii::$app->controller->id == 'settings' ? 'active' : '' ?>">
                            <a href="<?= Url::toRoute(['settings/index']) ?>">
                                <i class="icon-settings"></i>
                                <span class="title">Настройки</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="<?= Yii::$app->controller->id == 'apple' ? 'active' : '' ?>">
                        <a href="<?= Url::toRoute(['apple/index']) ?>">
                            <i class="icon-reports"></i>
                            <span class="title">Отчеты apple</span>
                        </a>
                    </li>
                </ul>
                <!-- END SIDEBAR MENU -->
            </div>
        </div>
        <!-- END SIDEBAR -->

        <!-- BEGIN CONTENT -->
        <div class="page-content-wrapper">
            <div class="page-content">
                <!-- BEGIN PAGE HEADER-->
                <h3 class="page-title">
                    <?= $this->title ?>
                </h3>
                <div class="page-bar">
                    <ul class="page-breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <?= Html::a('Главная', '/admin/') ?>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li><?= $this->title ?></li>
                    </ul>
                </div>
                <!-- END PAGE HEADER-->
                <br>
                <!-- BEGIN PAGE CONTENT-->
                <div class="row">
                    <div class="col-md-12">
                        <?= $content ?>
                    </div>
                </div>
                <!-- END PAGE CONTENT-->
            </div>
        </div>
        <!-- END CONTENT -->

        <!-- BEGIN FOOTER -->
        <div class="page-footer">
            <div class="page-footer-inner">
                <?= date('Y') ?> &copy; <?= Yii::$app->name ?>.
            </div>
            <div class="page-footer-tools">
                <span class="go-top"><i class="fa fa-angle-up"></i></span>
            </div>
        </div>
    </div>
</div>
<!-- END FOOTER -->
<?php $this->endBody(); ?>
<?php $this->registerJs("App.init(); Layout.init();"); ?>
</body>
</html>
<?php $this->endPage(); ?>
