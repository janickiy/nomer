<?php
/* @var $this \yii\web\View */
/* @var $searchRequest \app\models\SearchRequest */
/* @var $message String */

use yii\helpers\Url;



?>
<?php if(!$searchRequest->user_id): ?>
    <div class="sinfo">
        <?=$message;?> - зарегистрируйтесь.
        <span class="btns"><a href="#signup" class="buy">Регистрация / Вход</a></span>
    </div>
<?php elseif($searchRequest->user_id && !$searchRequest->is_payed && !$searchRequest->user->is_test): ?>
    <div class="sinfo">
        <?=$message;?> - получите бесплатные проверки.
        <span class="btns"><a href="<?=Url::toRoute(["site/confirm"]);?>" class="buy">Получить бесплатные проверки</a></span>
    </div>
<?php elseif($searchRequest->user_id && !$searchRequest->is_payed && $searchRequest->user->is_test): ?>
    <div class="sinfo">
        <?=$message;?> - купите проверки.
        <span class="btns"><a href="<?=Url::toRoute(["pay/index"]);?>" class="buy">Купить проверки</a></span>
    </div>
<?php endif; ?>
