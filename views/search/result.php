<?php
/* @var $this \yii\web\View */
/* @var $isGuest boolean */
/* @var $index integer */
/* @var $operator array */
/* @var $is_cache boolean */
/* @var $searchRequest \app\models\SearchRequest */

use yii\helpers\Url;

if(!isset($operator)) $operator = false;

?>

<?php if(!$searchRequest->is_payed): ?>
    <div class="searchStatusInner <?=$searchRequest->user_id?"searchFinished":"searchErr";?>">
        <?php if($operator): ?>
            Поиск завершен, <?=\yii\helpers\ArrayHelper::getValue($operator, "operator");?>(<?=\yii\helpers\ArrayHelper::getValue($operator, "region");?>)
        <?php else: ?>
            Поиск завершен.
        <?php endif; ?>
        <?php /*<strong>ПОИСК ЗАВЕРШЕН ЧАСТИЧНО</strong>. Вам нужно <a href="#signup">зарегистрироваться</a> и вы сможете увидеть всю информацию. */ ?>
        <?php /*Вам нужно <a href="<?=Url::toRoute(["pay/index"]);?>">Купить доступ</a> или взять <a class="free" href="<?=Url::toRoute(["try/index"]);?>">Бесплатный тест</a>*/ ?>
    </div>
<?php else: ?>
    <?php if($searchRequest->source_id == \app\models\SearchRequest::SOURCE_MOBILE): ?>
        <div class="searchStatusInner searchFinished <?=$searchRequest->is_payed==2?"free":"";?>">
            <?php if($searchRequest->is_payed == 2): ?>
                Поиск бесплатный. Качество <?=$index;?>%. <?php if($operator): ?><?=$operator["operator"];?>(<?=$operator["region"];?>)<?php endif; ?>
            <?php else: ?>
                Поиск платный. Качество <?=$index;?>%. <?php if($operator): ?><?=$operator["operator"];?>(<?=$operator["region"];?>)<?php endif; ?>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="searchStatusInner searchFinished">
            <?php if($operator): ?>
                Поиск завершен, индекс использования номера <strong><?=$index;?>%</strong>, Оператор - <strong><?=$operator["operator"];?></strong>. Регион - <strong><?=$operator["region"];?></strong>
            <?php else: ?>
                Поиск завершен, индекс использования номера <strong><?=$index;?>%</strong>.
            <?php endif; ?>
            <?php if($searchRequest->is_payed == 2): ?>
                <br>Проверка была <b style="color: green; font-weight: bold;">бесплатна</b>, так как ничего не найдено!
            <?php endif; ?>
        </div>
    <?php endif;?>
<?php endif; ?>