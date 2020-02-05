<?php
/* @var $this \yii\web\View */
/* @var $result array */

use yii\helpers\Html;
?>

<?php if(isset($result["photo"])): ?>
    <img src="data:image/gif;base64,<?=$result["photo"];?>" height="50" style="margin-right: 7px;">
<?php else: ?>
    Фото не найдено
<?php endif; ?>