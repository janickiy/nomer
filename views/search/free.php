<?php
/* @var $this \yii\web\View */
/* @var $items array */
/* @var $operator array */
/* @var $searchRequest \app\models\SearchRequest */

use app\models\ResultCache;
use \yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$names = $photos = [];

if (isset($items)) {
    $names = array_filter(ArrayHelper::getColumn($items, "name"));
    $names = array_unique($names);
    $photos = array_filter(ArrayHelper::getColumn($items, "photo"));
}

if (!isset($jobCount)) $jobCount = 0;
$jobCount = (int)$jobCount;
$time = ((int)$jobCount + 1) * 5;
?>

<?php if($searchRequest->is_payed == -1): ?>

<?php else: ?>
<div class="results clfix">
    <?php if(count($names)): ?>
    <div class="resLeft">
        <div class="result" id="names">
            <div class="resultInner resultInnerFirst">
                <div class="resultTitle ic2">Информация</div>
                <div class="resultCont">
                    <ul class="names">
                        <?php foreach ($names as $name): ?>
                        <li><?=$name;?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="resRight">
        <div class="result" id="avatars">
            <div class="resultInner resultInnerFirst">
                <div class="resultTitle ic1">Возможные фотографии</div>
                <div class="resultCont">
                    <ul class="photos">
                        <?php foreach($photos as $photo): ?>
                            <li><?=Html::img("data:image/jpg;base64,".$photo, ["height" => 200]); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
        <p class="payments-info" style="margin-bottom: 50px"><img src="/img/pay/payments_info.png">Демо поиск по <b>2</b> источникам результатов не дал. Полная проверка будет проводится по <b>18</b> источникам, и скорей всего мы найдем для вас информацию.</p>
    <?php endif; ?>
</div>
<?php endif; ?>


<?php if (false): ?>
<script><?php endif; ?>
    <?php ob_start(); ?>
    $('.popup').find('.close').bind('click', function () {
        $(this).parent().hide();
    });
    $('.infos li').bind('click', function () {
        $('.popup').show();
    });
    <?php $js = ob_get_contents(); ob_end_clean(); $this->registerJs($js); ?>
