<?php
/* @var $this \yii\web\View */
/* @var $log \app\models\SearchRequest[] */
/* @var $phone string */
/* @var $result array */
/* @var $id int */
/* @var $is_cache boolean */

use app\components\PhoneHelper;
use yii\helpers\Url;

$is_vip = false;
if(!\Yii::$app->user->isGuest && \Yii::$app->user->getIdentity()->is_vip) {
    $is_vip = true;
}

$seoPhone = preg_replace("/^7(\d\d\d)(\d\d\d)(\d\d)(\d\d)$/", "8 ($1) $2-$3-$4", $phone);

$this->title = 'Информация по номеру телефона: '.join(", ", PhoneHelper::getFormats($phone));
?>
<div class="row">
    <div class="col-md-offset-3 col-md-6 col-xs-12">
        <h1 class="header__title">Информация по номеру телефона:<br><?=$seoPhone;?></h1>
    </div>
</div>

<br>

<!--noindex-->
<div id="results">

    <?php if(isset($result["cache"]) && $result["cache"]): ?>
        <p style="text-align: center; color: #FFF;">Информация взята из кэша.<br>Нажмите <a href="<?=Url::toRoute(["result/index", "phone" => preg_replace("/^7/", "8", $phone), "refresh" => 1]);?>">здесь</a>, чтобы обновить информацию не из кэша</p>
    <?php endif; ?>

    <ul class="form-tabs" id="avatars" style="display: none;">
        <li class="form-tabs__item"><span class="form-tabs__link">Возможные фотографии<p>Всем</p></span></li><!--
        --><li class="form-tabs__item"><span class="form-tabs__link"></span></li>
    </ul>

    <ul class="form-tabs" id="names" style="display: none;">
        <li class="form-tabs__item"><span class="form-tabs__link">Возможные имена<p>Всем</p></span></li><!--
        --><li class="form-tabs__item"><span class="form-tabs__link"></span></li>
    </ul>

    <?php if(isset($result["mobile"])): ?>
    <ul class="form-tabs" id="operator">
        <li class="form-tabs__item"><span class="form-tabs__link">Оператор, регион<p>Гости</p></span></li><!--
        --><li class="form-tabs__item"><span class="form-tabs__link"><?=$result["mobile"]["operator"];?>, <?=$result["mobile"]["region"];?></span></li>
    </ul>
    <?php endif; ?>

    <ul class="form-tabs" id="vk_2012">
        <li class="form-tabs__item"><span class="form-tabs__link">В контакте 2012<p>Платные</p></span></li><!--
        --><li class="form-tabs__item"><span class="form-tabs__link" style="max-width: 300px;">идет поиск...</span><span class="time"></span></li>
    </ul>

    <ul class="form-tabs" id="facebook">
        <li class="form-tabs__item"><span class="form-tabs__link">Facebook<p>Платные</p></span></li><!--
        --><li class="form-tabs__item"><span class="form-tabs__link">идет поиск...</span><span class="time"></span></li>
    </ul>

    <ul class="form-tabs" id="avito">
        <li class="form-tabs__item"><span class="form-tabs__link">Avito<p>Зарегистрированные</p></span></li><!--
        --><li class="form-tabs__item"><span class="form-tabs__link">идет поиск...</span><span class="time"></span></li>
    </ul>

    <ul class="form-tabs" id="avinfo">
        <li class="form-tabs__item"><span class="form-tabs__link">auto.ru<p>Зарегистрированные</p></span></li><!--
        --><li class="form-tabs__item"><span class="form-tabs__link">идет поиск...</span><span class="time"></span></li>
    </ul>

    <ul class="form-tabs" id="google">
        <li class="form-tabs__item"><span class="form-tabs__link">Google<p>Всем</p></span></li><!--
        --><li class="form-tabs__item"><span class="form-tabs__link">идет поиск...</span><span class="time"></span></li>
    </ul>

    <ul class="form-tabs" id="mamba">
        <li class="form-tabs__item"><span class="form-tabs__link">Mamba<p>Всем</p></span></li><!--
        --><li class="form-tabs__item"><span class="form-tabs__link">идет поиск...</span><span class="time"></span></li>
    </ul>

    <ul class="form-tabs" id="summary">
        <li class="form-tabs__item"><span class="form-tabs__link">Итого</span></li>
        <li class="form-tabs__item"><span class="form-tabs__link">0%</span></li>
    </ul>

    <ul class="form-tabs" id="log">
        <li class="form-tabs__item"><span class="form-tabs__link">Данный номер телефона<br>искали до вас <?=count($log)?> раз(а)</span></li>
        <li class="form-tabs__item"><span class="form-tabs__link"><?php if(count($log)) foreach($log as $l) { echo \Yii::$app->formatter->asDatetime($l->tm, "dd.MM.yyyy в HH:mm")."<br>";}; ?></span></li>
    </ul>
</div>
<!--/noindex-->

<?php
if(!is_null($id)) {
    $this->registerJs("NomerIoApp.init(".$is_vip."); NomerIopp.socket().emit('search', { id: ".$id."})", \yii\web\View::POS_READY, "search");
}
?>