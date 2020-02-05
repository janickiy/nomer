<?php
/* @var $this \yii\web\View */
/* @var $items [] */
/* @var $phone int */
/* @var $resultAntiparkon [] */
/* @var $gibddResult [] */

use yii\helpers\Html;
?>
<?php if($items || count($resultAntiparkon) || count($gibddResult)): ?>
    <ul>
        <?php if(is_array($gibddResult)) foreach($gibddResult as $r): ?>
            <li><?=preg_replace("/\w/", "*", $r["number"]);?>, <?=preg_replace("/\w/", "*", $r["model"]);?></li>
        <?php endforeach; ?>
        <?php if(is_array($resultAntiparkon)) foreach($resultAntiparkon as $r): ?>
            <li><?=preg_replace("/\w/", "*", $r["number"]);?>, <?=preg_replace("/\w/", "*", $r["marka"]);?></li>
        <?php endforeach; ?>
        <?php if(is_array($items)) foreach($items as $i): ?>
            <li><?=$i["credate"];?>, <?=preg_replace("/\w/", "*", $i["marka"]." ".$i["model"]);?>, <?=$i["year"];?>г., <?=preg_replace("/\w/", "*", $i["city"]);?>, <?=preg_replace("/\w/", "*", $i["price"]);?>р.</li>
        <?php endforeach; ?>
    </ul>
    <div class="sinfo">
        Если хотите увидеть информацию без звездочек - зарегистрируйтесь.
        <span class="btns"><a href="#signup" class="buy">Регистрация / Вход</a></span>
    </div>
<?php else: ?>
    Ничего не найдено
<?php endif; ?>