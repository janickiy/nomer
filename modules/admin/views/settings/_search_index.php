<?php

/* @var $this \yii\web\View */

use yii\helpers\Html;
use app\models\Settings;

?>

<?= Html::beginForm() ?>
<table class="table">
    <tr>
        <th colspan="2">Индексы поиска</th>
    </tr>
    <tr>
        <td>Индекс поиска оператора</td>
        <td><?=Html::textInput('search_index_operator', Settings::get('search_index_operator', 0), ['class' => 'form-control']) ?></td>
    </tr>
    <tr>
        <td>Индекс поиска фото в Viber</td>
        <td><?=Html::textInput('search_index_viber', Settings::get('search_index_viber', 7), ['class' => 'form-control']) ?></td>
    </tr>
    <tr>
        <td>Индекс поиска во ВКонтакте VIP</td>
        <td><?=Html::textInput('search_index_vk_vip', Settings::get('search_index_vk_vip', 0), ['class' => 'form-control']) ?></td>
    </tr>
    <tr>
        <td>Индекс поиска во ВКонтакте</td>
        <td><?=Html::textInput('search_index_vk', Settings::get('search_index_vk', 15), ['class' => 'form-control']) ?></td>
    </tr>
    <tr>
        <td>Индекс поиска в Facebook</td>
        <td><?=Html::textInput('search_index_fb', Settings::get('search_index_fb', 15), ['class' => 'form-control']) ?></td>
    </tr>
    <tr>
        <td>Индекс поиска по Avito</td>
        <td><?=Html::textInput('search_index_avito', Settings::get('search_index_avito', 15), ['class' => 'form-control']) ?></td>
    </tr>
    <tr>
        <td>Индекс поиска в Google</td>
        <td><?= Html::textInput('search_index_google', Settings::get('search_index_google', 7), ["class" => 'form-control']) ?></td>
    </tr>
    <tr>
        <td>Индекс поиска по Auto.ru</td>
        <td><?= Html::textInput('search_index_avinfo', Settings::get('search_index_avinfo', 15), ['class' => 'form-control']) ?></td>
    </tr>
    <tr>
        <td>Индекс поиска в Sprut</td>
        <td><?= Html::textInput('search_index_sprut', Settings::get('search_index_sprut', 0), ['class' => 'form-control']) ?></td>
    </tr>
    <tr>
        <td>Индекс поиска в Truecaller</td>
        <td><?= Html::textInput('search_index_truecaller', Settings::get('search_index_truecaller', 15), ['class' => 'form-control']) ?></td>
    </tr>
    <tr>
        <td>Индекс поиска в Numbuster</td>
        <td><?= Html::textInput('search_index_numbuster', Settings::get('search_index_numbuster', 15), ['class' => 'form-control']) ?></td>
    </tr>
    <tr>
        <td>Индекс поиска в Инстаграмме</td>
        <td><?= Html::textInput('search_index_instagram', Settings::get('search_index_instagram', 20), ['class' => 'form-control']) ?></td>
    </tr>
    <tr>
        <td>Индекс поиска в Антипарконе</td>
        <td><?= Html::textInput('search_index_antiparkon', Settings::get('search_index_antiparkon', 5), ['class' => 'form-control']) ?></td>
    </tr>
    <tr>
        <td>Индекс поиска в Телеграмме</td>
        <td><?= Html::textInput('search_index_telegram', Settings::get('search_index_telegram', 7), ['class' => 'form-control']) ?></td>
    </tr>
</table>
<?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
<?= Html::endForm() ?>
