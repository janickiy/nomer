<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = Html::encode($message);
?>

<div class="searchBox">
    <div class="cont clfix">
        <div class="row">
            <div class="col-md-offset-3 col-md-6 col-xs-12">
                <h1 class="header__title"><?= nl2br(Html::encode($message)) ?></h1>
            </div>
        </div>
    </div>
</div>