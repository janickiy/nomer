<?php
/* @var $this \yii\web\View */
/* @var $log \app\models\SearchRequest[] */
/* @var $result array */
/* @var $id int */
/* @var $is_cache boolean */
/* @var $searchRequest \app\models\SearchRequest */

use app\components\PhoneHelper;
use app\models\RequestResult;
use app\models\ResultCache;
use app\models\UrlFilter;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Html;

$is_guest = \Yii::$app->user->isGuest;
$is_test = false;
$is_vip = false;
$is_admin = false;

if (!$is_guest) {
    /* @var $user \app\models\User */
    $user = \Yii::$app->user->getIdentity();
    if ($user->is_vip) {
        $is_vip = true;
    }
    if ($user->is_admin) {
        $is_admin = true;
    }
}

$phone = ArrayHelper::getValue($searchRequest, "phone");

$seoPhone = preg_replace("/^7(\d\d\d)(\d\d\d)(\d\d)(\d\d)$/", "8 ($1) $2-$3-$4", $phone);

$this->title = 'Информация по номеру телефона: ' . join(", ", PhoneHelper::getFormats($phone));

$phones = PhoneHelper::getFormats($phone);

$index = array_sum(ArrayHelper::getColumn($searchRequest->results, "index"));

$operator = RequestResult::find()->where(["request_id" => $searchRequest->id, "type_id" => ResultCache::TYPE_OPERATOR])->one();
if ($operator) $operator = Json::decode($operator->data);

$basic = [
    "phones" => [],
    "emails" => []
];

/*
$years = [];

foreach($years as $year) {
    $age = date("Y") - $year;
    $elements[] = ["name" => "Предполагаемый возраст: ".($age-1)." - ".($age+1)];
}
*/

$years = [];

$ch = curl_init('http://ssd.nomer.io/api/'.$phone.'?token=NWBpdeqbbAFJMVYJU6XAfhyydeyhgX');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if($httpCode == 200) { // Все ок, берем данные
    $response = Json::decode($response);
    foreach($response as $r)  {
        switch($r["type"]) {
            case "phone":
                if(\Yii::$app->getUser()->getIdentity()->is_vip) {
                    $basic["phones"][] = $r["data"];
                }

                break;
            case "email":
                if(\Yii::$app->getUser()->getIdentity()->is_vip) {
                    if (strpos($r["data"], '@') !== false) {
                        $basic["emails"][] = $r["data"];
                    }
                }
                break;
            case "birthday":
                $year = $r["data"];
                $yearRows = explode(".", $year);
                foreach($yearRows as $yearRow) {
                    if(strlen($yearRow) == 4) {
                        $years[] = $yearRow;
                    }
                }
                break;
        }
    }
}

$photos = $names = [];

$namesIndex = $photosIndex = $autoIndex = 0;

$facebook = RequestResult::find()->where(["request_id" => $searchRequest->id, "type_id" => ResultCache::TYPE_FACEBOOK])->one();
$facebookResult = null;
$facebookIndex = 0;
if ($facebook) {
    $facebookResult = Json::decode($facebook->data);
    $facebookIndex = ArrayHelper::getValue($facebook, "index");
    foreach ($facebookResult as $fbItem) {
        if (isset($fbItem["photo"])) $photos[] = $is_admin?["facebook", $fbItem["photo"]]:$fbItem["photo"];
        if (isset($fbItem["name"])) $names[] = ["facebook", $fbItem["name"]];
    }
}

$vk = RequestResult::find()->where(["request_id" => $searchRequest->id, "type_id" => ResultCache::TYPE_VK_2012])->one();
$vkResult = null;
$vkIndex = 0;
if ($vk) {
    $vkResult = Json::decode($vk->data);
    $vkIndex = ArrayHelper::getValue($vk, "index");
    foreach ($vkResult as $vkItem) {
        if (isset($vkItem["photo"])) $photos[] = $is_admin?["vk_2012", $vkItem["photo"]]:$vkItem["photo"];
        if (isset($vkItem["name"])) $names[] = ["vk_2012", $vkItem["name"]];
    }
}

$vkVip = RequestResult::find()->where(["request_id" => $searchRequest->id, "type_id" => ResultCache::TYPE_VK])->one();
$vkVipResult = null;
$vkVipIndex = 0;
if ($vkVip) {
    $vkVipResult = Json::decode($vkVip->data);
    $vkVipIndex = ArrayHelper::getValue($vkVip, "index");
    foreach ($vkVipResult as $vkVipItem) {
        if (isset($vkVipItem["photo"])) $photos[] = $is_admin?["vk", $vkVipItem["photo"]]:$vkVipItem["photo"];
        if (isset($vkVipItem["name"])) $names[] = ["vk", $vkVipItem["name"]];
    }
}

$avito = RequestResult::find()->where(["request_id" => $searchRequest->id, "type_id" => ResultCache::TYPE_AVITO])->one();
$avitoResult = [];
$avitoIndex = 0;
if ($avito) {
    $avitoResult = Json::decode($avito->data);

    $avitoIndex = ArrayHelper::getValue($avito, "index");
    $avitoNames = [];
    foreach($avitoResult as $avitoItem) {
        $avitoNames[] = $avitoItem["name"];
    }
    $avitoNames = array_unique($avitoNames);
    if(count($avitoNames)) {
        foreach($avitoNames as $avitoName) {
            $names[] = ["avito", $avitoName];
        }
    }
}

$google = RequestResult::find()->where(["request_id" => $searchRequest->id, "type_id" => ResultCache::TYPE_GOOGLE_PHONE])->one();
$googleResult = [];
$googleIndex = 0;
if ($google) {
    $googleResult = Json::decode($google->data);
    if (isset($googleResult["items"])) $googleResult["result"] = $googleResult["items"];
    $googleIndex = ArrayHelper::getValue($google, "index");
}

$auto = RequestResult::find()->where(["request_id" => $searchRequest->id, "type_id" => ResultCache::TYPE_AVINFO_API])->one();
$autoResult = null;
if ($auto) {
    $autoResult = Json::decode($auto->data);
    if(isset($autoResult["auto"])) $autoResult = $autoResult["auto"];
    $autoIndex += ArrayHelper::getValue($auto, "index");
}

$antiparkon = RequestResult::find()->where(["request_id" => $searchRequest->id, "type_id" => ResultCache::TYPE_ANTIPARKON])->one();
$antiparkonResult = null;
$antiparkonIndex = 0;
if ($antiparkon) {
    $antiparkonResult = Json::decode($antiparkon->data);
    foreach ($antiparkonResult as $r) {
        $names[] = ["Антипаркон", $r["name"]];
    }
    $autoIndex += ArrayHelper::getValue($antiparkon, "index");
}

$gibdd = RequestResult::find()->where(["request_id" => $searchRequest->id, "type_id" => ResultCache::TYPE_GIBDD])->one();
$gibddResult = null;
$gibddIndex = 0;
if ($gibdd) {
    $gibddResult = Json::decode($gibdd->data);
    foreach ($gibddResult as $r) {
        $names[] = ["Гибдд", $r["name"]];
    }
    $autoIndex += ArrayHelper::getValue($gibdd, "index");
}

$scorista = RequestResult::find()->where(["request_id" => $searchRequest->id, "type_id" => ResultCache::TYPE_SCORISTA])->one();
$scoristaResult = null;
$scoristaIndex = 0;
if ($scorista) {
    $scoristaResult = $scorista->data;
    $scoristaIndex = ArrayHelper::getValue($scorista, "index");
}

$viber = RequestResult::find()->where(["request_id" => $searchRequest->id, "type_id" => ResultCache::TYPE_VIBER])->one();
$viberResult = null;
$viberIndex = 0;
if ($viber) {
    $viberResult = Json::decode($viber->data);
    $viberIndex = ArrayHelper::getValue($viber, "index");
    if (isset($viberResult["name"])) $names[] = ["viber", $viberResult["name"]];
    if (isset($viberResult["photo"])) {
        $photos[] = $is_admin ? ["viber", $viberResult["photo"]] : $viberResult["photo"];
        $photosIndex += 5;
    }
}

$truecaller = RequestResult::find()->where(["request_id" => $searchRequest->id, "type_id" => ResultCache::TYPE_TRUECALLER])->one();
$truecallerResult = null;
$truecallerIndex = 0;
if ($truecaller) {
    $truecallerResults = Json::decode($truecaller->data);
    $truecallerIndex = ArrayHelper::getValue($truecaller, "index");
    $namesIndex += $truecallerIndex;
    foreach($truecallerResults as $truecallerResult) {
        if (isset($truecallerResult["name"])) $names[] = ["truecaller", $truecallerResult["name"]];
        if (isset($truecallerResult["photo"])) {
            $photos[] = $is_admin ? ["truecaller", $truecallerResult["photo"]] : $truecallerResult["photo"];
            $photosIndex += 7;
        }
    }
}

$numbuster = RequestResult::find()->where(["request_id" => $searchRequest->id, "type_id" => ResultCache::TYPE_NUMBUSTER])->one();
$numbusterResult = null;
$numbusterIndex = 0;
if ($numbuster) {
    $numbusterResults = Json::decode($numbuster->data);
    $numbusterIndex = ArrayHelper::getValue($numbuster, "index");
    $namesIndex += $numbusterIndex;
    if(is_array($numbusterResults)) foreach ($numbusterResults as $numbusterResult) {
        if (isset($numbusterResult["name"])) $names[] = ["numbuster", $numbusterResult["name"]];
        if (isset($numbusterResult["photo"])) {
            $photos[] = $is_admin ? ["numbuster", $numbusterResult["photo"]] : $numbusterResult["photo"];
            $photosIndex += 7;
        }
    }
}

$getcontact = RequestResult::find()->where(["request_id" => $searchRequest->id, "type_id" => ResultCache::TYPE_GETCONTACT])->one();
$getcontactResult = null;
$getcontactIndex = 0;
if ($getcontact) {
    $getcontactResults = Json::decode($getcontact->data);
    $getcontactIndex = ArrayHelper::getValue($getcontact, "index");
    $namesIndex += $getcontactIndex;
    if(is_array($getcontactResults)) foreach ($getcontactResults as $getcontactResult) {
        if (isset($getcontactResult["name"])) $names[] = ["numbuster", $getcontactResult["name"]];
    }
}

$telegram = RequestResult::find()->where(["request_id" => $searchRequest->id, "type_id" => ResultCache::TYPE_TELEGRAM])->one();
$telegramResult = null;
$telegramIndex = 0;
if ($telegram) {
    $telegramResult = Json::decode($telegram->data);
    $telegramIndex = ArrayHelper::getValue($telegram, "index");
    $namesIndex += $telegramIndex;
    if (isset($telegramResult["name"])) $names[] = ["telegram", $telegramResult["name"]];
    if (isset($telegramResult["photo"])) {
        $photos[] = $is_admin ? ["telegram", $telegramResult["photo"]] : $telegramResult["photo"];
        $photosIndex += 5;
    }
}

$instagram = RequestResult::find()->where(["request_id" => $searchRequest->id, "type_id" => ResultCache::TYPE_INSTAGRAM])->one();
$instagramResult = null;
$instagramIndex = 0;
if ($instagram) {
    $instagramResult = Json::decode($instagram->data);
    $instagramIndex = ArrayHelper::getValue($instagram, "index");
    foreach ($instagramResult as $instItem) {
        if (isset($instItem["photo"])) $photos[] = $is_admin ? ["instagram", $instItem["photo"]] : $instItem["photo"];
        if (isset($instItem["name"])) $names[] = ["instagram", $instItem["name"]];
    }
}
?>

<?= $this->render("_form", ["phone" => $phone]); ?>

<div class="searchBox">
    <div class="cont clfix">

        <div class="searchStatus">
            <div class="searchStatusInner searchFinished">
                <?php if(\Yii::$app->devicedetect->isMobile() || \Yii::$app->devicedetect->isTablet()): ?>
                    Информация взята из кеша. Качество <strong><?= $index; ?>%</strong>
                    <?php if ($operator && is_array($operator) && count($operator) && isset($operator["mobile"])): ?>
                        <?= $operator["mobile"]["operator"]; ?>(<?= $operator["mobile"]["region"]; ?>)
                    <?php endif; ?>.
                <?php else: ?>
                    Информация взята из кеша, индекс использования номера <strong><?= $index; ?>%</strong>
                    <?php if ($operator && is_array($operator) && count($operator) && isset($operator["mobile"])): ?>
                        Оператор - <strong><?= $operator["mobile"]["operator"]; ?></strong>. Регион -
                        <strong><?= $operator["mobile"]["region"]; ?></strong>
                    <?php endif; ?>.
                <?php endif; ?>
            </div>
            <div class="searchInner" style="margin-bottom: 20px;">
                <input class="searchBtn inpBtn" id="refresh" value="Обновить данные" type="submit"
                       >
            </div>
        </div>
        <div class="results clfix">
            <?php if(\Yii::$app->devicedetect->isMobile() || \Yii::$app->devicedetect->isTablet()): ?>
                <?php if ($photos): ?>
                    <div class="result" id="avatars">
                        <div class="resultInner resultInnerFirst">
                            <div class="parcent">0%</div>
                            <div class="resultTitle ic1">Возможные фотографии</div>
                            <div class="resultCont">
                                <ul class="photos">
                                    <?php foreach ($photos as $photo): ?>
                                        <?php if ($is_admin): list($type, $photo) = $photo; $photo = preg_match('/^http/', $photo)?preg_replace("/\'\./", "", $photo):"data:image/jpg;base64,".$photo; ?>
                                            <li class="s_<?= $type; ?>"><a href="<?=$photo;?>" class="swipebox"><?= Html::img($photo); ?></a></li>
                                        <?php else: $photo = preg_match('/^http/ium', $photo)?preg_replace("/\'\./", "", $photo):"data:image/jpg;base64,".$photo; ?>
                                            <li>
                                                <a href="<?=$photo;?>" class="swipebox">
                                                    <?= Html::img($photo, ["rel" => "lightbox"]); ?>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($names || count($basic["phones"]) || count($basic["emails"])): ?>
                    <div class="result" id="names">
                        <div class="resultInner resultInnerFirst">
                            <div class="parcent"><?=$telegramIndex+$numbusterIndex+$instagramIndex+$truecallerIndex?>%</div>
                            <div class="resultTitle ic2">Информация</div>
                            <div class="resultCont">
                                <ul class="names">
                                    <?php if(count($basic["phones"])) foreach($basic["phones"] as $bp): ?>
                                        <li>basic: <?= $bp; ?></li>
                                    <?php endforeach; ?>
                                    <?php if(count($basic["emails"])) foreach($basic["emails"] as $be): ?>
                                        <li>basic: <?= $be; ?></li>
                                    <?php endforeach; ?>
                                    <?php foreach($years as $year) {
                                    $age = date("Y") - $year;
                                        echo ($is_vip?("Предполагаемый возраст: ".$age):("Предполагаемый возраст: ".($age-1)." - ".($age+1)))."<br>";
                                    } ?>
                                    <?php foreach ($names as list($type, $n)): ?>
                                        <li<?=in_array($type, ["truecaller", "numbuster"])?" class='green'":"";?>><?= $is_admin?$type.": ":""; ?><?= $n; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($is_vip): ?>
                    <div class="result socRes" id="scorista">
                        <div class="resultInner">
                            <div class="parcent"><?= $scoristaIndex; ?>%</div>
                            <div class="resultTitle scorista">Скориста</div>
                            <div class="resultCont">
                                <?= $this->render("/search/scorista", [
                                    "searchRequest" => $searchRequest,
                                    "scoristaResult" => $scoristaResult
                                ]); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="result socRes<?= $instagramIndex == 0 ? " resultNo" : ""; ?>" id="instagram">
                    <div class="resultInner">
                        <div class="parcent"><?=$instagramIndex;?>%</div>
                        <div class="resultTitle ic9">instagram<span class="sTime"></span></div>
                        <div class="resultCont">
                            <?= $this->render("/search/instagram", [
                                "result" => $instagramResult,
                                "searchRequest" => $searchRequest,
                                'cache' => true
                            ]); ?>
                        </div>
                    </div>
                </div>
                <div class="result socRes" id="vk_2012">
                    <div class="resultInner">
                        <div class="parcent"><?= $vkIndex; ?>%</div>
                        <div class="resultTitle ic4">ВКОНТАКТЕ<span class="sTime"></span></div>
                        <div class="resultCont">
                            <?= $this->render("/search/vk", [
                                "result" => $vkResult,
                                "searchRequest" => $searchRequest,
                                'cache' => true
                            ]); ?>
                        </div>
                    </div>
                </div>
                <?php if ($is_vip): ?>
                    <div class="result socRes" id="vk">
                        <div class="resultInner">
                            <div class="parcent"><?= $vkVipIndex; ?>%</div>
                            <div class="resultTitle ic4">ВКОНТАКТЕ VIP<span class="sTime"></span></div>
                            <div class="resultCont">
                                <?= $this->render("/search/vk", [
                                    "result" => $vkVipResult,
                                    "searchRequest" => $searchRequest,
                                    'cache' => true
                                ]); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="result" id="avito">
                    <div class="resultInner">
                        <div class="parcent"><?= $avitoIndex; ?>%</div>
                        <div class="resultTitle ic6">AVITO.RU<span class="sTime"></span></div>
                        <div class="resultCont">
                            <?= $this->render("/search/avito", [
                                "result" => $avitoResult,
                                "phone" => $phone,
                                "searchRequest" => $searchRequest
                            ]); ?>
                        </div>
                    </div>
                </div>
                <div class="result" id="avinfo">
                    <div class="resultInner">
                        <div class="parcent"><?= $autoIndex ?>%</div>
                        <div class="resultTitle ic5">AUTO.RU<span class="sTime"></span></div>
                        <div class="resultCont">
                            <?= $this->render("/search/avinfo", [
                                "searchRequest" => $searchRequest,
                                "items" => $autoResult,
                                "phone" => $phone,
                                "resultAntiparkon" => $antiparkonResult,
                                "gibddResult" => $gibddResult]);
                            ?>
                        </div>
                    </div>
                </div>

                <div class="result" id="google">
                    <div class="resultInner">
                        <div class="parcent"><?= $googleIndex; ?>%</div>
                        <div class="resultTitle ic7">Google<span class="sTime"></span></div>
                        <div class="resultCont">
                            <?= $this->render("/search/google", [
                                "items" => isset($googleResult["result"]) ? $googleResult["result"] : $googleResult,
                                "phone" => $phone,
                                'urls' => ArrayHelper::map(UrlFilter::find()->where(['type' => UrlFilter::TYPE_BANNED])->all(), 'url', 'type'),
                            ]); ?>
                        </div>
                    </div>
                </div>

                <div class="result socRes" id="facebook">
                    <div class="resultInner">
                        <div class="parcent"><?= $facebookIndex; ?>%</div>
                        <div class="resultTitle ic3">facebook<span class="sTime"></span></div>
                        <div class="resultCont">
                            <?= $this->render("/search/facebook", [
                                "result" => $facebookResult,
                                "searchRequest" => $searchRequest,
                                'cache' => true
                            ]); ?>
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
                            <div class="parcent"><?= $sprutIndex; ?>%</div>
                            <div class="resultTitle ic10">Скориста</div>
                            <div class="resultCont">
                                <?= $this->render("/search/sprut", ["items" => $sprutResult, "phone" => $phone]); ?>
                            </div>
                        </div>
                    </div>
                <?php endif;*/ ?>
            <?php else: ?>
                <div class="resLeft">
                    <?php if ($photos): ?>
                        <div class="result<?= $photosIndex == 0 ? " resultNo" : ""; ?>" id="avatars">
                            <div class="resultInner resultInnerFirst">
                                <div class="parcent"><?=$photosIndex;?>%</div>
                                <div class="resultTitle ic1">Возможные фотографии</div>
                                <div class="resultCont">
                                    <ul class="photos">
                                        <?php foreach ($photos as $photo): ?>
                                            <?php if ($is_admin): list($type, $photo) = $photo; $photo = preg_match('/^http/ium', $photo)?preg_replace("/\'\./", "", $photo):"data:image/jpg;base64,".$photo; ?>
                                                <li class="s_<?= $type; ?>"><a href="<?=$photo;?>" class="swipebox"><?= Html::img($photo); ?></a></li>
                                            <?php else: $photo = preg_match('/^http/ium', $photo)?preg_replace("/\'\./", "", $photo):"data:image/jpg;base64,".$photo; ?>
                                                <li><a href="<?=$photo;?>" class="swipebox"><?= Html::img($photo); ?></a></li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if ($is_vip): ?>
                        <div class="result" id="scorista">
                            <div class="resultInner">
                                <div class="parcent"><?= $scoristaIndex; ?>%</div>
                                <div class="resultTitle scorista">Скориста</div>
                                <div class="resultCont">
                                    <?= $this->render("/search/scorista", [
                                        "searchRequest" => $searchRequest,
                                        "scoristaResult" => $scoristaResult
                                    ]); ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="result socRes<?= $instagramIndex == 0 ? " resultNo" : ""; ?>" id="instagram">
                        <div class="resultInner">
                            <div class="parcent"><?=$instagramIndex;?>%</div>
                            <div class="resultTitle ic9">instagram<span class="sTime"></span></div>
                            <div class="resultCont">
                                <?= $this->render("/search/instagram", [
                                    "result" => $instagramResult,
                                    "searchRequest" => $searchRequest,
                                    'cache' => true
                                ]); ?>
                            </div>
                        </div>
                    </div>
                    <div class="result<?= $avitoIndex == 0 ? " resultNo" : ""; ?>" id="avito">
                        <div class="resultInner">
                            <div class="parcent"><?= $avitoIndex; ?>%</div>
                            <div class="resultTitle ic6">AVITO.RU<span class="sTime"></span></div>
                            <div class="resultCont">
                                <?= $this->render("/search/avito", [
                                    "result" => $avitoResult,
                                    "phone" => $phone,
                                    "searchRequest" => $searchRequest,
                                    'cache' => true
                                ]); ?>
                            </div>
                        </div>
                    </div>

                    <div class="result<?= $googleIndex == 0 ? " resultNo" : ""; ?>" id="google">
                        <div class="resultInner">
                            <div class="parcent"><?= $googleIndex; ?>%</div>
                            <div class="resultTitle ic7">Google<span class="sTime"></span></div>
                            <div class="resultCont">
                                <?= $this->render("/search/google", [
                                    "items" => isset($googleResult["result"]) ? $googleResult["result"] : $googleResult,
                                    "phone" => $phone,
                                    'urls' => ArrayHelper::map(UrlFilter::find()->where(['type' => UrlFilter::TYPE_BANNED])->all(), 'url', 'type'),
                                ]); ?>
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

                </div>
                <div class="resRight">
                    <?php if ($names || count($basic["phones"]) || count($basic["emails"])): ?>
                        <div class="result<?= $namesIndex == 0 ? " resultNo" : ""; ?>" id="names">
                            <div class="resultInner resultInnerFirst">
                                <div class="parcent"><?=$namesIndex?>%</div>
                                <div class="resultTitle ic2">Информация</div>
                                <div class="resultCont">
                                    <ul class="names">
                                        <?php if(count($basic["phones"])) foreach($basic["phones"] as $bp): ?>
                                            <li>basic: <?= $bp; ?></li>
                                        <?php endforeach; ?>
                                        <?php if(count($basic["emails"])) foreach($basic["emails"] as $be): ?>
                                            <li>basic: <?= $be; ?></li>
                                        <?php endforeach; ?>
                                        <?php foreach($years as $year) {
                                            $age = date("Y") - $year;
                                            echo $is_vip?"Предполагаемый возраст: ".$age:"Предполагаемый возраст: ".($age-1)." - ".($age+1);
                                        } ?>
                                        <?php foreach ($names as list($type, $n)): ?>
                                            <li<?=in_array($type, ["truecaller", "numbuster"])?" class='green'":"";?>><?= $is_admin?$type.": ":""; ?><?= $n; ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="result socRes<?= $vkIndex == 0 ? " resultNo" : ""; ?>" id="vk_2012">
                        <div class="resultInner">
                            <div class="parcent"><?= $vkIndex; ?>%</div>
                            <div class="resultTitle ic4">ВКОНТАКТЕ<span class="sTime"></span></div>
                            <div class="resultCont">
                                <?= $this->render("/search/vk", [
                                    "result" => $vkResult,
                                    "searchRequest" => $searchRequest,
                                    'cache' => true
                                ]); ?>
                            </div>
                        </div>
                    </div>
                    <?php if ($is_vip): ?>
                        <div class="result socRes<?= $vkVipIndex == 0 ? " resultNo" : ""; ?>" id="vk">
                            <div class="resultInner">
                                <div class="parcent"><?= $vkVipIndex; ?>%</div>
                                <div class="resultTitle ic4">ВКОНТАКТЕ VIP<span class="sTime"></span></div>
                                <div class="resultCont">
                                    <?= $this->render("/search/vk", [
                                        "result" => $vkVipResult,
                                        "searchRequest" => $searchRequest,
                                        'cache' => true
                                    ]); ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="result<?= ($autoIndex) == 0 ? " resultNo" : ""; ?>" id="avinfo">
                        <div class="resultInner">
                            <div class="parcent"><?= $autoIndex; ?>%</div>
                            <div class="resultTitle ic5">AUTO.RU<span class="sTime"></span></div>
                            <div class="resultCont">
                                <?= $this->render("/search/avinfo", [
                                    "searchRequest" => $searchRequest,
                                    "items" => $autoResult,
                                    "phone" => $phone,
                                    "resultAntiparkon" => $antiparkonResult,
                                    "gibddResult" => $gibddResult
                                ]); ?>
                            </div>
                        </div>
                    </div>
                    <div class="result socRes<?= $facebookIndex == 0 ? " resultNo" : ""; ?>" id="facebook">
                        <div class="resultInner">
                            <div class="parcent"><?= $facebookIndex; ?>%</div>
                            <div class="resultTitle ic3">facebook<span class="sTime"></span></div>
                            <div class="resultCont">
                                <?= $this->render("/search/facebook", [
                                    "result" => $facebookResult,
                                    "searchRequest" => $searchRequest,
                                    'cache' => true
                                ]); ?>
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

<?php if(false): ?><script><?php endif; ?>
    <?php ob_start(); ?>
//
    $('#refresh').click(function() {
        $.confirm({
            theme: 'supervan',
            title: 'Обновление информации',
            content: 'Внимание! Будет сделано обновление данных и оно ПЛАТНОЕ! Нажмите Да, если согласны на оплату',
            buttons: {
                confirm: {
                    text: 'Да',
                    action: function () {
                        location.href = '<?= Url::toRoute(["result/index", "phone" => preg_replace("/^7/", "8", $phone), "refresh" => 1]); ?>'
                    }
                },
                cancel: {
                    text: 'Отмена',
                    action: function () {

                    }
                }
            }
        });
    });

    <?php $js = ob_get_contents(); ob_get_clean(); $this->registerJs($js); ?>
