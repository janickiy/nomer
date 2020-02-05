<?php
/* @var $this \yii\web\View */
/* @var $phone string */
/* @var $result array */
/* @var $id int */

use app\components\PhoneHelper;

$seoPhone = preg_replace("/^7(\d\d\d)(\d\d\d)(\d\d)(\d\d)$/", "8 ($1) $2-$3-$4", $phone);

//$this->title = 'Информация по номеру телефона: '.join(", ", PhoneHelper::getFormats($phone));
?>
<div class="search searchInner">
    <div class="clfix">

        <h2>Потратьте лучше деньги, чем время.</h2>

        <p>Мы отправили уведомление проверяемому номеру <?=$seoPhone;?> о том, что вы его пытались пробить. Надеюсь вы больше не будете пытаться нас обмануть и купите тариф. Спасибо</p>
    </div>
</div>