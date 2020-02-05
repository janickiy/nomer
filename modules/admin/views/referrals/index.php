<?php
/* @var $this \yii\web\View */

$this->title = "Реферальная система";
?>

<table class="table">
    <tr>
        <th>Пользователь</th>
        <th>Кол-во рефералов</th>
        <th>Баланс</th>
    </tr>
    <?php foreach($refs as $r): ?>
        <tr>
            <td><?=$r->email;?></td>
            <td><?=\app\models\User::find()->where(["ref_id" => $r->id])->count();?></td>
            <td><?=$r->ref_balance;?></td>
        </tr>
    <?php endforeach; ?>
</table>
