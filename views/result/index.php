<?php
/* @var $this \yii\web\View */
/* @var $log \app\models\SearchRequest[] */
/* @var $is_cache boolean */
/* @var $searchRequest \app\models\SearchRequest */

use app\components\PhoneHelper;
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
?>

<?=$this->render("_form", ["phone" => $phone]); ?>

<div class="searchBox">
    <div class="cont clfix">

        <div class="searchStatus">
            <div class="searchStatusInner searchLoading">
                Идёт поиск информации по номеру <?= $seoPhone; ?>. Ждите
            </div>
        </div>
        <div class="results clfix<?=\Yii::$app->getUser()->isGuest?" superponer-wrap":"";?>">
            <?php if(\Yii::$app->devicedetect->isMobile() || \Yii::$app->devicedetect->isTablet()): ?>
                <div class="result" id="avatars" style="display: none">
                    <div class="resultInner resultInnerFirst">
                        <div class="parcent">0%</div>
                        <div class="resultTitle ic1">Возможные фотографии</div>
                        <div class="resultCont">
                            <ul class="photos"></ul>
                            <?php if($is_guest): ?>
                                <div class="sinfo">
                                    Если хотите увидеть фотографии, зарегистрируйтесь.
                                    <span class="btns"><a href="#signup" class="buy">Регистрация / Вход</a></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="result" id="names" style="display: none">
                    <div class="resultInner resultInnerFirst">
                        <div class="parcent">0%</div>
                        <div class="resultTitle ic2">Информация</div>
                        <div class="resultCont">
                            <ul class="names"></ul>
                            <?php if($is_guest): ?>
                                <div class="sinfo">
                                    Если хотите увидеть имена без звездочек, зарегистрируйтесь.
                                    <span class="btns"><a href="#signup" class="buy">Регистрация / Вход</a></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php if ($is_vip): ?>
                    <div class="result socRes" id="scorista">
                        <div class="resultInner">
                            <div class="parcent">0%</div>
                            <div class="resultTitle scorista">Скориста</div>
                            <div class="resultCont">
                                <p class="loading"><img src="/img/sload.gif"><span>идет поиск...</span></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="result socRes" id="instagram">
                    <div class="resultInner">
                        <div class="parcent">0%</div>
                        <div class="resultTitle ic9">instagram<span class="sTime"></span></div>
                        <div class="resultCont">
                            <p class="loading"><img src="/img/sload.gif"><span>идет поиск...</span></p>
                        </div>
                    </div>
                </div>
                <div class="result socRes" id="vk_2012">
                    <div class="resultInner">
                        <div class="parcent">0%</div>
                        <div class="resultTitle ic4">ВКОНТАКТЕ<span class="sTime"></span></div>
                        <div class="resultCont">
                            <p class="loading"><img src="/img/sload.gif"><span>идет поиск...</span></p>
                        </div>
                    </div>
                </div>
                <?php if ($is_vip): ?>
                    <div class="result socRes" id="vk">
                        <div class="resultInner">
                            <div class="parcent">0%</div>
                            <div class="resultTitle ic4">ВКОНТАКТЕ VIP<span class="sTime"></span></div>
                            <div class="resultCont">
                                <p class="loading"><img src="/img/sload.gif"><span>идет поиск...</span></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="result" id="avito">
                    <div class="resultInner">
                        <div class="parcent">0%</div>
                        <div class="resultTitle ic6">AVITO.RU<span class="sTime"></span></div>
                        <div class="resultCont">
                            <p class="loading"><img src="/img/sload.gif"><span>идет поиск...</span></p>
                        </div>
                    </div>
                </div>
                <div class="result" id="avinfo">
                    <div class="resultInner">
                        <div class="parcent">0%</div>
                        <div class="resultTitle ic5">AUTO.RU<span class="sTime"></span></div>
                        <div class="resultCont">
                            <p class="loading"><img src="/img/sload.gif"><span>идет поиск...</span></p>
                        </div>
                    </div>
                </div>

                <div class="result resultNo" id="google">
                    <div class="resultInner">
                        <div class="parcent">0%</div>
                        <div class="resultTitle ic7">Google<span class="sTime"></span></div>
                        <div class="resultCont">
                            <?php if(\Yii::$app->getUser()->isGuest): ?>
                                <?=$this->render("/search/guest");?>
                            <?php else: ?>
                                <div class="sinfo">В связи с низкой востребованностью мы не ищем по-умолчанию, если вам нужен поиск в Google нажмите кнопку
                                    <span class="btns"><a href="javascript:;" class="buy" data-action="search" data-type="google" data-id="<?=$searchRequest->id;?>">Искать</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="result socRes" id="facebook">
                    <div class="resultInner">
                        <div class="parcent">0%</div>
                        <div class="resultTitle ic3">facebook<span class="sTime"></span></div>
                        <div class="resultCont">
                            <p class="loading"><img src="/img/sload.gif"><span>идет поиск...</span></p>
                        </div>
                    </div>
                </div>

                <?php if(count($log)):?>
                    <div class="result">
                        <div class="resultInner">
                            <div class="resultTitle">Кто искал</div>
                            <div class="resultCont">

                                <ul>
                                    <?php foreach(array_splice($log, 0, 10) as $l): ?>
                                        <li>
                                            <a href="<?=Url::toRoute(["admin/users/view", "id" => $l["user_id"]]);?>">
                                            <?=$l["tm"];?>;
                                            <?=$l["ip"];?>;
                                            <?php
                                            switch($l["source_id"]) {
                                                case \app\models\SearchRequest::SOURCE_WEB: echo "Desktop"; break;
                                                case \app\models\SearchRequest::SOURCE_MOBILE: echo "Mobile"; break;
                                                case \app\models\SearchRequest::SOURCE_IOS: echo "IOS"; break;
                                                case \app\models\SearchRequest::SOURCE_ANDROID: echo "Android"; break;
                                                default: echo "Не определн";
                                            }
                                            ?>;
                                            <?php if($l["user_id"]): ?>
                                                <?=$l["user"]["email"];?>
                                            <?php else: ?>
                                                Аноним
                                            <?php endif; ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php if(count($log) > 10): ?>
                                    <?=\yii\helpers\Html::a("Все результаты", ["result/log", "phone" => $phone]);?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php /*if ($is_vip): ?>
                    <div class="result" id="scorista">
                        <div class="resultInner">
                            <div class="parcent">0%</div>
                            <div class="resultTitle ic10">Скориста</div>
                            <div class="resultCont">
                                <div class="sinfo">В связи с низкой востребованностью мы не ищем по-умолчанию, если вам нужен поиск в Скористе нажмите кнопку
                                    <span class="btns"><a href="javascript:;" class="buy" data-action="search" data-type="scorista" data-id="<?=$searchRequest->id;?>">Искать</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif;*/ ?>
            <?php else: ?>
                <?php /*if(\Yii::$app->getUser()->isGuest):?>
                    <div class="superponer-block-info">Для того, чтобы увидеть<br>всю информацию, вам необходимо <a href="<?=Url::toRoute(["site/signup"]);?>">зарегистрироваться</a></div>
                <?php endif;*/ ?>
                <?php /* if(!\Yii::$app->getUser()->isGuest && !$searchRequest->is_payed):?>
                    <div class="superponer-block-info nopic">Для того, чтобы увидеть<br>всю информацию, вам необходимо <a href="<?=Url::toRoute(["pay/index"]);?>">пополнить счет</a></div>
                <?php endif;*/ ?>
                <div class="resLeft" <?=(\Yii::$app->getUser()->isGuest)?" style='opacity:1'":"";?>>
                    <div class="result" id="avatars" style="display: none">
                        <div class="resultInner resultInnerFirst">
                            <div class="parcent">0%</div>
                            <div class="resultTitle ic1">Возможные фотографии</div>
                            <div class="resultCont">
                                <ul class="photos"></ul>
                                <?=$this->render("/_parts/_btns", [
                                    "searchRequest" => $searchRequest,
                                    "message" => "Если хотите увидеть фотографии"
                                ]); ?>
                            </div>
                        </div>
                    </div>
                    <?php if ($is_vip): ?>
                        <div class="result" id="scorista">
                            <div class="resultInner">
                                <div class="parcent">0%</div>
                                <div class="resultTitle scorista">Скориста</div>
                                <div class="resultCont">
                                    <p class="loading"><img src="/img/sload.gif"><span>идет поиск...</span></p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="result socRes" id="instagram">
                        <div class="resultInner">
                            <div class="parcent">0%</div>
                            <div class="resultTitle ic9">instagram<span class="sTime"></span></div>
                            <div class="resultCont">
                                <p class="loading"><img src="/img/sload.gif"><span>идет поиск...</span></p>
                            </div>
                        </div>
                    </div>
                    <div class="result" id="avito">
                        <div class="resultInner">
                            <div class="parcent">0%</div>
                            <div class="resultTitle ic6">AVITO.RU<span class="sTime"></span></div>
                            <div class="resultCont">
                                <p class="loading"><img src="/img/sload.gif"><span>идет поиск...</span></p>
                            </div>
                        </div>
                    </div>

                    <div class="result resultNo" id="google">
                        <div class="resultInner">
                            <div class="parcent">0%</div>
                            <div class="resultTitle ic7">Google<span class="sTime"></span></div>
                            <div class="resultCont">
                                <?php if(\Yii::$app->getUser()->isGuest): ?>
                                    <?=$this->render("/search/guest");?>
                                <?php else: ?>
                                    <div class="sinfo">В связи с низкой востребованностью мы не ищем по-умолчанию, если вам нужен поиск в Google нажмите кнопку
                                        <span class="btns"><a href="javascript:;" class="buy" data-action="search" data-type="google" data-id="<?=$searchRequest->id;?>">Искать</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <?php if(count($log)):?>
                        <div class="result">
                            <div class="resultInner">
                                <div class="resultTitle">Кто искал</div>
                                <div class="resultCont">

                                    <ul>
                                        <?php foreach(array_splice($log, 0, 10) as $l): /* @var $l \app\models\SearchRequest*/ ?>
                                            <li>
                                                <?php if(preg_match("/TelegramBot/", $l["ua"])): ?>
                                                    Antiparkon:
                                                <?php endif; ?>
                                                <a href="<?=Url::toRoute(["admin/users/view", "id" => $l["user_id"]]);?>">
                                                <?=$l["tm"];?>;
                                                <?=$l["ip"];?>;
                                                <?php
                                                switch($l["source_id"]) {
                                                    case \app\models\SearchRequest::SOURCE_WEB: echo "Desktop"; break;
                                                    case \app\models\SearchRequest::SOURCE_MOBILE: echo "Mobile"; break;
                                                    case \app\models\SearchRequest::SOURCE_IOS: echo "IOS"; break;
                                                    case \app\models\SearchRequest::SOURCE_ANDROID: echo "Android"; break;
                                                    default: echo "Не определн";
                                                }
                                                ?>;
                                                <?php if($l["user_id"]): ?>
                                                    <?=$l["user"]["email"];?>
                                                <?php else: ?>
                                                    Аноним
                                                <?php endif; ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <?php if(count($log) > 10): ?>
                                        <?=\yii\helpers\Html::a("Все результаты", ["result/log", "phone" => $phone]);?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="resRight" <?=\Yii::$app->getUser()->isGuest?" style='opacity:1'":"";?>>
                    <div class="result" id="names" style="display: none">
                        <div class="resultInner resultInnerFirst">
                            <div class="parcent">0%</div>
                            <div class="resultTitle ic2">Информация</div>
                            <div class="resultCont">
                                <ul class="names"></ul>
                                <?=$this->render("/_parts/_btns", [
                                    "searchRequest" => $searchRequest,
                                    "message" => "Если хотите увидеть имена без звездочек"
                                ]); ?>
                            </div>
                        </div>
                    </div>
                    <div class="result socRes" id="vk_2012">
                        <div class="resultInner">
                            <div class="parcent">0%</div>
                            <div class="resultTitle ic4">ВКОНТАКТЕ<span class="sTime"></span></div>
                            <div class="resultCont">
                                <p class="loading"><img src="/img/sload.gif"><span>идет поиск...</span></p>
                            </div>
                        </div>
                    </div>
                    <?php if ($is_vip): ?>
                        <div class="result socRes" id="vk">
                            <div class="resultInner">
                                <div class="parcent">0%</div>
                                <div class="resultTitle ic4">ВКОНТАКТЕ VIP<span class="sTime"></span></div>
                                <div class="resultCont">
                                    <p class="loading"><img src="/img/sload.gif"><span>идет поиск...</span></p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="result" id="avinfo">
                        <div class="resultInner">
                            <div class="parcent">0%</div>
                            <div class="resultTitle ic5">AUTO.RU<span class="sTime"></span></div>
                            <div class="resultCont">
                                <p class="loading"><img src="/img/sload.gif"><span>идет поиск...</span></p>
                            </div>
                        </div>
                    </div>
                    <div class="result socRes" id="facebook">
                        <div class="resultInner">
                            <div class="parcent">0%</div>
                            <div class="resultTitle ic3">facebook<span class="sTime"></span></div>
                            <div class="resultCont">
                                <p class="loading"><img src="/img/sload.gif"><span>идет поиск...</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>



        <div class="bottInfo">Представлена информация по номеру <?= $phones[0]; ?>. Все объявления avito с
            номера <?= $phones[1]; ?>. Также какие машины продавались с номера <?= $phones[2]; ?>. Какие анкеты были
            зарегистрированы на номер <?= $phones[3]; ?>
            в социальных сетях И предоставили всю информацию по номеру <?= $phones[4]; ?> в google и yandex. Ниже
            представлена вся информация доступная в интернете по номеру телефона <?= $seoPhone; ?></div>
    </div>
</div>

<?php
if ($searchRequest->id) {
    $this->registerJs("NomerIoApp.socket().emit('search', { id: " . $searchRequest->id . "})", \yii\web\View::POS_READY, "search");
}
?>

