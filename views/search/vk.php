<?php
/* @var $this \yii\web\View */
/* @var $result array */
/* @var $phone $phone */
/* @var $searchRequest \app\models\SearchRequest */

use yii\helpers\Html;
use yii\helpers\Url;

if($result) foreach($result as $i => $item) {
    if(!isset($item["name"])) unset($result[$i]);
}
?>
<?php if(count($result)): ?>
    <?php foreach($result as $item): ?>
        <?php if(isset($item["photo"])) {
            $item["photo"] = preg_match('/^http/ium', $item["photo"])?preg_replace("/\'\./", "", $item["photo"]):"data:image/jpg;base64,".$item["photo"];
        } ?>
    <div class="vkp">
        <?php if(isset($item["link"])): ?>
            <?=Html::a((isset($item["photo"])?Html::img($item["photo"]):"").$item["name"], $item["link"], ["target" => "_blank"]);?><br>
		<?php elseif(isset($item["id"])):?>
			<?=Html::a((isset($item["photo"])?Html::img($item["photo"]):"").$item["name"], "https://vk.com/id".$item["id"], ["target" => "_blank"]);?><br>
                <?php else: ?>
                    <?=(isset($item["photo"])?Html::img($item["photo"]):"").$item["name"];?><br>
                <?php endif; ?>
                <?php if(isset($item["raw"]) && $searchRequest->user_id && $searchRequest->user->is_vip): ?>
                    <p><?=$item["raw"];?></p>
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
    <?php if(isset($is_guest) && $is_guest): ?>
        <?=$this->render("guest");?>
    <?php else: ?>
        <p class="no">Ничего не найдено</p>
    <?php endif; ?>
<?php endif; ?>
