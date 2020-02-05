
<?php
/* @var $this \yii\web\View */
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = "Пополнение счета";
?>

<div class="registration">
    <div class="cont clfix">
        <h2>Вам нужно пополнить счет</h2>

        <a href="<?=Url::toRoute(["pay/index"]);?>">Перейти к пополнению баланса</a>
    </div>
</div>
