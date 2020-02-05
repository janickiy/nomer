<?php

/* @var $this \yii\web\View */
/* @var $items array */
/* @var $urls array */
/* @var $phone int */

use yii\helpers\Html;

$items = array_filter($items, function($row) use ($urls) {
    list($url) = $row;
    $shortUrl = urldecode(preg_replace('/(http|https)\:\/\/(.+?)\/(.*)/', '$2', $url));
    return !array_key_exists($shortUrl, $urls);
});
?>

<?php if($items): ?>
    <div class="allRes"><?=Html::a("Найдено результатов: <span>".count($items)."</span>", ["result/google", "phone" => preg_replace('/^7/', '8', $phone)], ["target" => "_blank"]);?></div>
    <?php if(isset($is_vip) && $is_vip && isset($photos)): ?>
        <p>Поиск был по <?=$photos;?> фотографиям</p>
    <?php endif;?>
<?php else: ?>
    <p class="no">Ничего не найдено</p>
<?php endif; ?>

