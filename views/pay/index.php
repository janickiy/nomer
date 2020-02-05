<?php
/* @var $this \yii\web\View */

use app\components\CostsHelper;
use app\models\Repost;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = "Пополнение счета";

$hasRepost = Repost::find()->where(["user_id" => \Yii::$app->getUser()->getId()])->count(1);

$host = preg_replace("/www\./", "", $_SERVER["HTTP_HOST"]);

$site = \app\models\Site::find()->where(["name" => $host])->one();
?>

    <div class="breadcrumbs">
        <ul class="breadcrumb">
            <?php if(\Yii::$app->devicedetect->isMobile() || \Yii::$app->devicedetect->isTablet()): ?>
                <li><?= Html::a('Главная', Url::home()) ?></li>
                <li>Покупка проверок</li>
            <?php else: ?>
                <li><?= Html::a('Главная', Url::home()) ?></li>
                <li>Покупка проверок</li>
            <?php endif; ?>
        </ul>
    </div>

<div class="page-content">
    <div class="cont clfix">
        <h1>Покупка проверок</h1>

        <?php if(\Yii::$app->devicedetect->isMobile() || \Yii::$app->devicedetect->isTablet()): ?>
            <ul class="packages">
                <li>2 проверка за репост "В контакте" <a class="button" href="<?=Url::toRoute(["pay/repost"]);?>">получить бесплатно</a></li>
                <li>0% &mdash; 1 проверка за <?=CostsHelper::getCost(1, $site->id);?> руб. <a class="button" href="<?=Url::toRoute(["pay/methods", "sum" => CostsHelper::getCostTotal(1, $site->id)]);?>">купить за <?=CostsHelper::getCostTotal(1, $site->id);?> руб.</a></li>
                <li>20% &mdash; 10 проверок по <?=CostsHelper::getCost(10, $site->id);?> руб. за шт. <a class="button" href="<?=Url::toRoute(["pay/methods", "sum" => CostsHelper::getCostTotal(10, $site->id)]);?>">купить за <?=CostsHelper::getCostTotal(10, $site->id);?> руб.</a></li>
                <li>30% &mdash; 20 проверок по <?=CostsHelper::getCost(20, $site->id);?> руб. за шт. <a class="button" href="<?=Url::toRoute(["pay/methods", "sum" => CostsHelper::getCostTotal(20, $site->id)]);?>">купить за <?=CostsHelper::getCostTotal(20, $site->id);?> руб.</a></li>
                <li>50% &mdash; 50 проверок по <?=CostsHelper::getCost(50, $site->id);?> руб. за шт. <a class="button" href="<?=Url::toRoute(["pay/methods", "sum" => CostsHelper::getCostTotal(50, $site->id)]);?>">купить за <?=CostsHelper::getCostTotal(50, $site->id);?> руб.</a></li>
                <li>60% &mdash; 100 проверок по <?=CostsHelper::getCost(100, $site->id);?> руб. за шт. <a class="button" href="<?=Url::toRoute(["pay/methods", "sum" => CostsHelper::getCostTotal(100, $site->id)]);?>">купить за <?=CostsHelper::getCostTotal(100, $site->id);?> руб.</a></li>
                <li>70% &mdash; 300 проверок по <?=CostsHelper::getCost(300, $site->id);?> руб. за шт. <a class="button" href="<?=Url::toRoute(["pay/methods", "sum" => CostsHelper::getCostTotal(300, $site->id)]);?>">купить за <?=CostsHelper::getCostTotal(300, $site->id);?> руб.</a></li>
                <li>75% &mdash; 500 проверок по <?=CostsHelper::getCost(500, $site->id);?> руб. за шт. <a class="button" href="<?=Url::toRoute(["pay/methods", "sum" => CostsHelper::getCostTotal(500, $site->id)]);?>">купить за <?=CostsHelper::getCostTotal(500, $site->id);?> руб.</a></li>
                <?php /*<li>40% &mdash; 300 проверок по 30 руб. за шт. <a class="button" href="<?=Url::toRoute(["pay/methods", "sum" => 9000]);?>">купить за 9000 руб.</a></li>*/ ?>
            </ul>
        <?php else:?>
            <?php if(false): ?>
            <div class="repost">
                <p class="title">3 проверки за репост <span>бесплатно</span></p>
                <p class="descr">Вы получите <span>3 проверки бесплатно</span> за репост в контакте!</p>
                <?= Html::beginForm(["pay/repost"], 'get'); ?>

                <?=Html::submitButton("Сделать репост", ['class' => 'button']); ?>
                <?=Html::endForm(); ?>
            </div>
            <?php endif; ?>
            <div class="packages">
                <div class="packages-header">
                    <div>Скидка</div>
                    <div>Кол-во проверок</div>
                    <div>Стоимость проверки</div>
                    <div>Цена пакета</div>
                    <div></div>
                </div>
                <?php if(!$hasRepost): ?>
                <div class="package" style="padding-top: 15px; padding-bottom: 15px;">
                    <a class="button" href="<?=Url::toRoute(["pay/repost"]);?>">2 проверки за репост "В контакте" получить бесплатно</a>
                </div>
                <?php endif; ?>
                <div class="package">
                    <div><span>0%</span></div>
                    <div><span>1</span> проверка</div>
                    <div><span><?=CostsHelper::getCost(1, $site->id);?></span> руб.</div>
                    <div><span><?=CostsHelper::getCostTotal(1, $site->id);?></span> руб.</div>
                    <div><a class="button" href="<?=Url::toRoute(["pay/methods", "sum" => CostsHelper::getCostTotal(1, $site->id)]);?>">купить</a></div>
                </div>
                <div class="package">
                    <div><span>20%</span></div>
                    <div><span>10</span> проверок</div>
                    <div><span><?=CostsHelper::getCost(10, $site->id);?></span> руб.</div>
                    <div><span><?=CostsHelper::getCostTotal(10, $site->id);?></span> руб.</div>
                    <div><a class="button" href="<?=Url::toRoute(["pay/methods", "sum" => CostsHelper::getCostTotal(10, $site->id)]);?>">купить</a></div>
                </div>
                <div class="package">
                    <div><span>30%</span></div>
                    <div><span>20</span> проверок</div>
                    <div><span><?=CostsHelper::getCost(20, $site->id);?></span> руб.</div>
                    <div><span><?=CostsHelper::getCostTotal(20, $site->id);?></span> руб.</div>
                    <div><a class="button" href="<?=Url::toRoute(["pay/methods", "sum" => CostsHelper::getCostTotal(20, $site->id)]);?>">купить</a></div>
                </div>
                <div class="show-more"><span onclick="$('.hide').removeClass('hide'); $(this).parent().remove();">Показать все тарифы</span></div>
                <div class="package hide">
                    <div><span>50%</span></div>
                    <div><span>50</span> проверок</div>
                    <div><span><?=CostsHelper::getCost(50, $site->id);?></span> руб.</div>
                    <div><span><?=CostsHelper::getCostTotal(50, $site->id);?></span> руб.</div>
                    <div><a class="button" href="<?=Url::toRoute(["pay/methods", "sum" => CostsHelper::getCostTotal(50, $site->id)]);?>">купить</a></div>
                </div>
                <div class="package hide">
                    <div><span>60%</span></div>
                    <div><span>100</span> проверок</div>
                    <div><span><?=CostsHelper::getCost(100, $site->id);?></span> руб.</div>
                    <div><span><?=CostsHelper::getCostTotal(100, $site->id);?></span> руб.</div>
                    <div><a class="button" href="<?=Url::toRoute(["pay/methods", "sum" => CostsHelper::getCostTotal(100, $site->id)]);?>">купить</a></div>
                </div>
                <div class="package hide">
                    <div><span>70%</span></div>
                    <div><span>300</span> проверок</div>
                    <div><span><?=CostsHelper::getCost(300, $site->id);?></span> руб.</div>
                    <div><span><?=CostsHelper::getCostTotal(300, $site->id);?></span> руб.</div>
                    <div><a class="button" href="<?=Url::toRoute(["pay/methods", "sum" => CostsHelper::getCostTotal(300, $site->id)]);?>">купить</a></div>
                </div>
                <div class="package hide">
                    <div><span>75%</span></div>
                    <div><span>500</span> проверок</div>
                    <div><span><?=CostsHelper::getCost(500, $site->id);?></span> руб.</div>
                    <div><span><?=CostsHelper::getCostTotal(500, $site->id);?></span> руб.</div>
                    <div><a class="button" href="<?=Url::toRoute(["pay/methods", "sum" => CostsHelper::getCostTotal(500, $site->id)]);?>">купить</a></div>
                </div>
            </div>
        <?php endif; ?>

        <p class="custompay">Купите произвольное количество проверок:</p>
        <div class="custompay">
            <?= Html::textInput('nsum', 10, ["autocomplete" => "off", "maxlength" => 3]); ?>
            <?= Html::hiddenInput('sum', CostsHelper::getCostTotal(10, $site->id)); ?>
            <p>Общая сумма: <span><?= CostsHelper::getCostTotal(10, $site->id);?>  руб</span></p>
            <?=Html::submitButton("Купить", ["class" => "inpBtn payBtn", "onclick" => new \yii\web\JsExpression("location.href='pay/methods?sum=' + $('input[name=\"sum\"]').val()")]); ?>
        </div>

        <p class="payments-info"><img src="/img/pay/payments_info.png"> Проверка будет списана, только если мы найдем что-то важное, например, соц.сети, информацию по машинам или объявлениям, в противном случае поиск для вас будет бесплатен и проверка вернется на баланс.</p>
    </div>
</div>

<?php if(false): ?><script><?php endif; ?>
    <?php ob_start(); ?>
    $('input[name="nsum"]').on('keyup', function() {
        var c = parseInt($(this).val(), 10);
        if(isNaN(c) || c < 0) {
            c = 1;
        }
        var sum = 0;
        if(c >= 500) {
            sum = c * 25;
        } else if(c >= 300) {
            sum = c * <?=CostsHelper::getCost(300, $site->id);?>;
        } else if(c >= 100) {
            sum = c * <?=CostsHelper::getCost(100, $site->id);?>;
        } else if(c >= 50) {
            sum = c * <?=CostsHelper::getCost(50, $site->id);?>;
        } else if(c >= 20) {
            sum = c * <?=CostsHelper::getCost(20, $site->id);?>;
        } else if(c >= 10) {
            sum = c * <?=CostsHelper::getCost(10, $site->id);?>;
        } else {
            sum = c * <?=CostsHelper::getCost(1, $site->id);?>
        }
        if(sum < <?=CostsHelper::getCost(1, $site->id);?>) sum = <?=CostsHelper::getCost(1, $site->id);?>;
        $('input[name="sum"]').val(sum);
        if(sum < <?=CostsHelper::getCost(1, $site->id);?>) sum = <?=CostsHelper::getCost(1, $site->id);?>;
        $('div.custompay').find('p').find('span').html(sum + ' руб');
    });

    <?php $js = ob_get_contents(); ob_end_clean(); $this->registerJs($js); ?>

    
