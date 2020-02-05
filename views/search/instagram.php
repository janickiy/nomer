<?php
/* @var $this \yii\web\View */
/* @var $result array */
/* @var $searchRequest \app\models\SearchRequest */

use yii\helpers\Html;
use yii\helpers\Url;

?>
<?php if(count($result)): ?>
    <?php foreach($result as $item): ?>
        <div class="faceb">
            <?php if(isset($item["link"])): ?>
                <?=Html::a((isset($item["photo"])?Html::img("data:image/jpg;base64,".$item["photo"]):"").\yii\helpers\ArrayHelper::getValue($item, "name", "имя не известно"), isset($item["link"])?$item["link"]:"#", ["target" => "_blank"]);?>
            <?php else: ?>
                <?=(isset($item["photo"])?Html::img("data:image/jpg;base64,".$item["photo"]):"").$item["name"];?>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p class="no">Ничего не найдено</p>
<?php endif; ?>
