<?php
/* @var $this \yii\web\View */

use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/* @var $searchRequest \app\models\SearchRequest */
?>

<?php if(is_null($scoristaResult)): ?>
    Ничего не найдено
<?php else: ?>
    <?=\yii\helpers\Html::a("Результаты поиска", ["result/scorista", "phone" => preg_replace('/^7/', '8', $searchRequest->phone)]);?>
    <?php
    $result = Json::decode($scoristaResult);
    $items = ArrayHelper::getValue($result, "DetailItems");
    ?>
    <div class="scorista_item">
        <?php foreach ($items[0] as $p): ?>
            <?php if(preg_match("/паспорт/iu", ArrayHelper::getValue($p, "title"))): continue; endif; ?>
            <?php if(trim(ArrayHelper::getValue($p, "value")) === ""): continue; endif; ?>
            <p>
                <b><?= ArrayHelper::getValue($p, "title"); ?></b>: <?= ArrayHelper::getValue($p, "value"); ?>
            </p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
