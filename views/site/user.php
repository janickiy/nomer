<?php

/* @var $this \yii\web\View */
/* @var $user array */

$this->title = Yii::$app->name . ' - ' . $user['first_name'] . ' ' . $user['last_name'];

?>

<div class="registration">
    <div class="cont clfix">
        <img src="<?= $user['photo_max_orig'] ?>" alt="<?= $user['first_name'] . ' ' . $user['last_name'] ?>">
        <h2><?= $user['first_name'] . ' ' . $user['last_name'] ?></h2>
        <h3><a href="https://vk.com/id<?= $user['id'] ?>">https://vk.com/id<?= $user['id'] ?></a></h3>
    </div>
</div>
