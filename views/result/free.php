<?php
/* @var $this \yii\web\View */
/* @var $log \app\models\SearchRequest[] */
/* @var $is_cache boolean */
/* @var $searchRequest \app\models\SearchRequest */

use app\components\PhoneHelper;
use app\models\ResultCache;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

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
}

$phone = ArrayHelper::getValue($searchRequest, "phone");

$seoPhone = preg_replace("/^7(\d\d\d)(\d\d\d)(\d\d)(\d\d)$/", "8 ($1) $2-$3-$4", $phone);

$this->title = 'Информация по номеру телефона: ' . join(", ", PhoneHelper::getFormats($phone));

$phones = PhoneHelper::getFormats($phone);

$operator = \app\models\RequestResult::find()->where(["request_id" => $searchRequest->id, "type_id" => ResultCache::TYPE_OPERATOR])->one();

$operator = \yii\helpers\Json::decode($operator->data);

if (!isset($jobCount)) $jobCount = 0;
$jobCount = (int)$jobCount;
$time = ((int)$jobCount + 1) * 5;

$siteTypeId = ArrayHelper::getValue($searchRequest, "site.type_id", 1);
?>

<?= $this->render("_form", ["phone" => $phone]); ?>

    <div class="searchBox">
    <?php if($siteTypeId == 1): ?>
        <div class="cont clfix">
            <p class="demo">Ваш бесплатный демо запрос поставлен в очередь на обработку.</p>
            <p class="payments-info"><img src="/img/pay/payments_info.png"> <span>Вы
                <b><?= ($jobCount + 1); ?></b> в очереди. Примерное время ожидание
                <b><?= \Yii::$app->formatter->asDuration($time, ' '); ?></b><br>Вы можете пополнить счет на <?=\Yii::$app->params["cost"];?> рублей, чтобы
                выполнить ПОЛНУЮ проверку номера в приоритетном порядке.</span></p>
        </div>
        <div class="line"></div>
    <?php endif; ?>

        <div class="cont clfix">
            <?php if($siteTypeId == 1 || ($siteTypeId == 2 && $searchRequest->is_payed == -1)): ?>
            <div class="searchStatus">
                <div class="searchStatusInner searchLoading">
                    Идёт поиск информации по номеру <?= $seoPhone; ?>. Ждите
                </div>
            </div>


            <div class="free-result">

            </div>
            <?php endif; ?>

            <?php if($siteTypeId == 1): ?>
            <div class="we-can-found">
                <p>Мы можем для вас найти по этому номеру<br>следующую информацию:</p>

                <div>
                    <div class="--left">
                        <ul>
                            <li><img src="/img/free/fb.png" width="32"><span>Узнать аккаунт на Facebook</span></li>
                            <li><img src="/img/free/vk.png" width="32"><span>Посмотреть анкету Вконтате</span></li>
                            <li><img src="/img/Logo-instagram.png" width="32"><span>Перейти в инстаграмм человека</span>
                            </li>
                        </ul>
                    </div>
                    <div class="--right">
                        <ul>
                            <li><img src="/img/free/avito.png" width="32"><span>Увидеть все объявления на Avito</span>
                            </li>
                            <li><img src="/img/free/autoru.png" width="32" style="margin-top: 8px;"><span>Все проданные автомобили на auto.ru</span>
                            </li>
                            <li><img src="/img/free/cars.png" width="32" style="margin-top: 8px;"><span>Увидеть все машины человека</span>
                            </li>
                            <ul>
                    </div>
                </div>

                <p>
                    <?php if ($searchRequest->user_id): ?>
                        <a href="<?= Url::toRoute(["pay/index"]); ?>" class="button">Купить информацию
                            за <?= \Yii::$app->params["cost"]; ?> руб.</a>
                    <?php else: ?>
                        <a href="#signup" class="button">Регистрация / Вход</a>
                    <?php endif; ?>
                </p>
            </div>
            <?php elseif($siteTypeId == 2 && ($searchRequest->is_payed != -1)): ?>
                <div class="we-can-found">
                    <p>Мы можем для вас найти по этому номеру<br>следующую информацию:</p>

                    <div>
                        <div class="--left">
                            <ul>
                                <li><img src="/img/free/fb.png" width="32"><span>Узнать аккаунт на Facebook</span></li>
                                <li><img src="/img/free/vk.png" width="32"><span>Посмотреть анкету Вконтате</span></li>
                                <li><img src="/img/Logo-instagram.png" width="32"><span>Перейти в инстаграмм человека</span>
                                </li>
                            </ul>
                        </div>
                        <div class="--right">
                            <ul>
                                <li><img src="/img/free/avito.png" width="32"><span>Увидеть все объявления на Avito</span>
                                </li>
                                <li><img src="/img/free/autoru.png" width="32" style="margin-top: 8px;"><span>Все проданные автомобили на auto.ru</span>
                                </li>
                                <li><img src="/img/free/cars.png" width="32" style="margin-top: 8px;"><span>Увидеть все машины человека</span>
                                </li>
                                <ul>
                        </div>
                    </div>

                    <p>
                        <?php if ($searchRequest->user_id): ?>
                            <a href="<?= Url::toRoute(["pay/index"]); ?>" class="button">Купить информацию
                                за <?= \Yii::$app->params["cost"]; ?> руб.</a>
                        <?php else: ?>
                            <a href="#signup" class="button">Регистрация / Вход</a>
                        <?php endif; ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="popup">
        <span class="close"></span>

        <?php if ($searchRequest->user_id): ?>
            <p>Данная информация предоставляется на платной основе. Стоимость одной проверки
                составляет <?= \Yii::$app->params["cost"]; ?> руб.</p>
            <div class="buttons"><a href="<?= Url::toRoute(["pay/index"]); ?>" class="button">Купить информацию
                    за <?= \Yii::$app->params["cost"]; ?> руб.</a></div>
        <?php else: ?>
            <p>Данная информация будет доступна после регистрации.</p>
            <div class="buttons"><a href="#signup" class="button">Регистрация / Вход</a></div>
        <?php endif; ?>
    </div>

<?php
if($siteTypeId == 1 || ($siteTypeId == 2 && $searchRequest->is_payed == -1)) {
    if ($searchRequest->id) {
        $this->registerJs("NomerIoApp.socket().emit('search', { id: " . $searchRequest->id . "})", \yii\web\View::POS_READY, "search");
    }
}
?>