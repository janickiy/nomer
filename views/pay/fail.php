<?php
/* @var $this \yii\web\View */
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = "Ошибка пополнения счета";

?>

    <div class="registration">
        <div class="cont clfix">
            <h2>Ошибка пополнения счета</h2>

            <a class="button" href="<?=Url::toRoute(["pay/index"]);?>">Поробовать ещё раз</a>
        </div>
    </div>
