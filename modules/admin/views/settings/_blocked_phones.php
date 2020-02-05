<?php

/* @var $this \yii\web\View */
/* @var $phones array */

$count = array_reduce($phones, function($acc, $data) {
    return [
        'all' => $acc['all'] += $data['all'],
        'unconfirmed' => $acc['unconfirmed'] += $data['confirmed'],
        'confirmed' => $acc['confirmed'] += $data['confirmed'],
        'vip' => $acc['vip'] += $data['vip']
    ];
}, ['all' => 0, 'unconfirmed' => 0, 'confirmed' => 0, 'vip' => 0]);

?>

<h3>Всего заблокировано <?= $count['all'] ?> номеров. Подтверждено: <?= $count['confirmed'] ?>. VIP <?= $count['vip'] ?></h3>

<table class="table table-striped table-bordered">
    <tr>
        <th>Дата</th>
        <th>Количество заблокированных мномеров</th>
    </tr>

    <?php foreach ($phones as $date => $data): ?>
        <tr>
            <td><?= $date ?></td>
            <td>Всего: <?= $data['all'] ?>, подтверждено: <?= $data['confirmed'] ?>, VIP: <?= $data['vip'] ?>.</td>
        </tr>
    <?php endforeach; ?>
</table>
