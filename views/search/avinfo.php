<?php
/* @var $this \yii\web\View */
/* @var $items [] */
/* @var $phone int */
/* @var $resultAntiparkon [] */
/* @var $gibddResult [] */
/* @var $searchRequest \app\models\SearchRequest */

use yii\helpers\Html;
use yii\helpers\Url;
use \yii\helpers\ArrayHelper;

if(!isset($items)) $items = [];
if(!isset($resultAntiparkon)) $resultAntiparkon = [];
if(!isset($gibddResult)) $gibddResult = [];
?>
<?php if(count($items) || count($resultAntiparkon) || count($gibddResult)): ?>
    <ul class="cars">
        <?php if(is_array($gibddResult)) foreach($gibddResult as $r): ?>
            <li>
                <b><?=ArrayHelper::getValue($searchRequest, ['user', 'is_admin'])?"Гибдд, Ездит на: ":"Ездит на: ";?></b><?=$r["number"];?>, <?=$r["model"];?>
            </li>
        <?php endforeach; ?>
        <?php if(is_array($resultAntiparkon)) foreach($resultAntiparkon as $r): ?>
            <li><b><?=ArrayHelper::getValue($searchRequest, ['user', 'is_admin'])?"Антипаркон: ":"Антипаркон: ";?></b><?=$r["number"];?>, <?=$r["marka"];?></li>
        <?php endforeach; ?>
        <?php if(is_array($items)) foreach($items as $i): ?>
            <li>
                <b><?=\yii\helpers\ArrayHelper::getValue($searchRequest, ["user", "is_admin"], false)?"Avinfo, Продавал(а): ":"Продавал(а): ";?></b><?=$i["credate"];?>, <?=$i["marka"];?> <?=$i["model"];?>, <?=$i["year"];?>г., <?=$i["city"];?><?=$i["price"]>0?", ".$i["price"]."р.":"";?>
                <?php if(isset($i["images"])): ?>

                    <?php
                    $images = array_filter(preg_split('/,/', $i["images"]));
                    ?>
                    <?php if(count($images)): ?>
                        <br>
                    <?php foreach ($images as $i => $img): if(preg_match("/http/", $img)){ continue; } $img = "https://qq.apinomer.com/cars/".$img; ?>
                        <a href="<?=$img;?>" class="swipebox"><img src="<?=$img;?>" width="100"></a>
                    <?php endforeach; ?>
                    <?php endif; ?>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    Ничего не найдено
<?php endif; ?>