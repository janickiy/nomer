<?php
/* @var $this \yii\web\View */
/* @var $phone String */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\MaskedInput;

$phone = preg_replace("/^(\d)(\d\d\d)(\d\d\d)(\d\d)(\d\d)$/", "+7 ($2) $3-$4-$5", $phone);

?>
<div class="searchWrap">
    <div class="search searchInner">
        <div class="cont clfix">
            <a href="<?= Url::home(); ?>" class="logo"><?=\Yii::$app->name;?></a>
            <h2 class="info">Информация по номеру телефона:</h2>
            <?= Html::beginForm(["search/index"]); ?>
            <div class="check">
                <?= Html::textInput("phone", $phone, [
                    'type' => 'tel',
                    'class' => 'searchPhone searchPhoneInner currNumber',
                    'placeholder' => '+7 (___) ___-__-__',
                ]);?>
                <?php /* MaskedInput::widget([
                    'name' => 'phone',
                    'value' => $phone,
                    'mask' => '+7 (999) 999-99-99',
                    'options' => [

                    ]
                ]);*/ ?>
                <img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz48IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4iICJodHRwOi8vd3d3LnczLm9yZy9HcmFwaGljcy9TVkcvMS4xL0RURC9zdmcxMS5kdGQiPjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiIHdpZHRoPSIzMnB4IiBoZWlnaHQ9IjMycHgiIHZpZXdCb3g9IjAgMCAzMiAzMiIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAwIDAgMzIgMzIiIHhtbDpzcGFjZT0icHJlc2VydmUiPjxnIGlkPSJzZWFyY2hfMV8iPjxwYXRoIGZpbGw9IiMzMzMzMzMiIGQ9Ik0yMCwwLjAwNWMtNi42MjcsMC0xMiw1LjM3My0xMiwxMmMwLDIuMDI2LDAuNTA3LDMuOTMzLDEuMzk1LDUuNjA4bC04LjM0NCw4LjM0MmwwLjAwNywwLjAwNkMwLjQwNiwyNi42MDIsMCwyNy40OSwwLDI4LjQ3N2MwLDEuOTQ5LDEuNTgsMy41MjksMy41MjksMy41MjljMC45ODUsMCwxLjg3NC0wLjQwNiwyLjUxNS0xLjA1OWwtMC4wMDItMC4wMDJsOC4zNDEtOC4zNGMxLjY3NiwwLjg5MSwzLjU4NiwxLjQsNS42MTcsMS40YzYuNjI3LDAsMTItNS4zNzMsMTItMTJDMzIsNS4zNzgsMjYuNjI3LDAuMDA1LDIwLDAuMDA1eiBNNC43OTUsMjkuNjk3Yy0wLjMyMiwwLjMzNC0wLjc2OCwwLjU0My0xLjI2NiwwLjU0M2MtMC45NzUsMC0xLjc2NS0wLjc4OS0xLjc2NS0xLjc2NGMwLTAuNDk4LDAuMjEtMC45NDMsMC41NDMtMS4yNjZsLTAuMDA5LTAuMDA4bDguMDY2LTguMDY2YzAuNzA1LDAuOTUxLDEuNTQ1LDEuNzkxLDIuNDk0LDIuNDk4TDQuNzk1LDI5LjY5N3ogTTIwLDIyLjAwNmMtNS41MjIsMC0xMC00LjQ3OS0xMC0xMGMwLTUuNTIyLDQuNDc4LTEwLDEwLTEwYzUuNTIxLDAsMTAsNC40NzgsMTAsMTBDMzAsMTcuNTI3LDI1LjUyMSwyMi4wMDYsMjAsMjIuMDA2eiIvPjxwYXRoIGZpbGw9IiMzMzMzMzMiIGQ9Ik0yMCw1LjAwNWMtMy44NjcsMC03LDMuMTM0LTcsN2MwLDAuMjc2LDAuMjI0LDAuNSwwLjUsMC41czAuNS0wLjIyNCwwLjUtMC41YzAtMy4zMTMsMi42ODYtNiw2LTZjMC4yNzUsMCwwLjUtMC4yMjQsMC41LTAuNVMyMC4yNzUsNS4wMDUsMjAsNS4wMDV6Ii8+PC9nPjwvc3ZnPg==" onclick="ga('send', 'event', 'button', 'click', 'search-button', $('[name=\'phone\']').val()); submit();">
                <?php if(!\Yii::$app->getUser()->isGuest): ?>
                    <div class="myProfileWrap">
                        <a class="myProfile" href="javascript:;">Мой профиль</a>
                        <?php if(!\Yii::$app->user->isGuest && (!isset($_SERVER["is_mobile"]) || $_SERVER["is_mobile"] == 0)): ?>
                            <?=$this->render("/_parts/_profile_menu");?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <span>введите новый номер для проверки</span>
            </div>
            <input type="submit" class="searchBtn inpBtn" value="Проверить новый номер" onclick="ga('send', 'event', 'button', 'click', 'search-button', $('[name=\'phone\']').val()">


            <?= Html::endForm(); ?>

        </div>
    </div>
</div>


