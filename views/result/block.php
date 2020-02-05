<?php
/* @var $this \yii\web\View */
use app\components\PhoneHelper;

/* @var $phone string */
/* @var $result array */
/* @var $id int */

$seoPhone = preg_replace("/^7(\d\d\d)(\d\d\d)(\d\d)(\d\d)$/", "8 ($1) $2-$3-$4", $phone);

$this->title = 'Информация по номеру телефона: '.join(", ", PhoneHelper::getFormats($phone));
?>
<div class="search searchInner">
    <div class="clfix">

        <h2>Номер <?=$seoPhone;?> заблокирован для поиска его владельцем</h2>
    </div>
</div>