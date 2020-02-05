<?php
/* @var $this \yii\web\View */
/* @var $result array */
/* @var $searchRequest \app\models\SearchRequest */

use yii\helpers\Html;
use yii\helpers\Url;

?>
<?php if(count($result)): ?>
    <?php foreach($result as $item): ?>
        <?php if(isset($item["photo"])) {
            $item["photo"] = preg_match('/^http/ium', $item["photo"])?preg_replace("/\'\./", "", $item["photo"]):"data:image/jpg;base64,".$item["photo"];
        } ?>
        <div class="faceb">
            <?php if(isset($item["link"])): ?>
                <?=Html::a((isset($item["photo"])?Html::img($item["photo"]):"").$item["name"], isset($item["link"])?$item["link"]:"#", ["target" => "_blank"]);?>
            <?php else: ?>
                <?=Html::img($item["photo"]).$item["name"];?>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    <?php if(!isset($cache)): ?>
    <?=$this->render("/_parts/_btns", [
        "searchRequest" => $searchRequest,
        "message" => "Если хотите увидеть фотографию, имя и ссылку на профиль"
    ]);?>
    <?php endif; ?>
<?php else: ?>
    <p class="no">Ничего не найдено</p>
<?php endif; ?>
