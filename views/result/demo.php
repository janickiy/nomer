<?php
/* @var $this \yii\web\View */
/* @var $phone string */
/* @var $operator array */
?>

<?=$this->render("_form", ["phone" => $phone]);?>
<div class="searchBox">
    <div class="cont clfix">
        <h2>Общая информация о <?=$phone;?></h2>
        <ul class="demo">
            <li>Телефон: <b><?=$phone;?></b></li>
            <li>Страна: <b>Россия</b></li>
            <?php if(isset($operator["region"])): ?>
                <li>Регион: <b><?=$operator["region"];?></b></li>
            <?php endif; ?>
            <?php if(isset($operator["operator"])): ?>
                <li>Оператор: <b><?=$operator["operator"];?></b></li>
            <?php endif; ?>
            <li>Международный формат: <b>+7 xxxxxxxxxx</b></li>
            <li>Национальный формат: <b>8 xxxxxxxxxx</b></li>
            <li>Номер телефона: <b><?=preg_replace('/^7/', '8', $phone);?></b></li>
            <li>Телефонный мобильный код: <b><?=preg_replace('/^7(\d\d\d)(\d\d\d\d\d\d\d)/', '$1', $phone);?></b></li>
        </ul>

        <h3 style="margin-top: 15px; margin-bottom: 7px;">Формы написания телефона</h3>

        <p>Список из всех возможных вариантов написания номера <?=$phone;?>:</p><br />

        <div class="demo">
            <div>
                <ul>
                    <li><?=preg_replace("/7(\d{3})(\d{7})/", "$2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{4})/", "$2 $3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{4})/", "$2-$3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{2})(\d{2})/", "$2 $3 $4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{2})(\d{2})/", "$2 $3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{2})(\d{2})/", "$2-$3-$4", $phone);?></li>
                    <li><?=$phone;?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{7})/", "7 $1$2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{7})/", "7-$1$2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{7})/", "7 $1 $2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{7})/", "7-$1-$2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{4})/", "7 $1 $2 $3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{4})/", "7 $1 $2-$3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{4})/", "7 $1-$2-$3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{4})/", "7-$1-$2-$3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{2})(\d{2})/", "7 $1 $2 $3 $4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{2})(\d{2})/", "7 $1 $2 $3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{2})(\d{2})/", "7 $1 $2-$3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{2})(\d{2})/", "7 $1-$2-$3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{2})(\d{2})/", "7-$1-$2-$3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{7})/", "7($1)$2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{4})/", "7($1)$2 $3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{4})/", "7($1)$2-$3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{2})(\d{2})/", "7($1)$2 $3 $4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{2})(\d{2})/", "7($1)$2 $3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{2})(\d{2})/", "7($1)$2-$3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{7})/", "7 ($1)$2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{7})/", "7 ($1) $2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{4})/", "7 ($1) $2 $3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{4})/", "7 ($1) $2-$3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{2})(\d{2})/", "7 ($1) $2 $3 $4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{2})(\d{2})/", "7 ($1) $2 $3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{2})(\d{2})/", "7 ($1) $2-$3-$4", $phone);?></li>
                    <li>+<?=$phone;?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{7})/", "+7 $1$2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{7})/", "+7-$1$2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{7})/", "+7 $1 $2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{7})/", "+7-$1-$2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{4})/", "+7 $1 $2 $3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{4})/", "+7 $1 $2-$3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{4})/", "+7 $1-$2-$3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{4})/", "+7-$1-$2-$3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{2})(\d{2})/", "+7 $1 $2 $3 $4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{2})(\d{2})/", "+7 $1 $2 $3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{2})(\d{2})/", "+7 $1 $2-$3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{2})(\d{2})/", "+7 $1-$2-$3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{2})(\d{2})/", "+7-$1-$2-$3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{7})/", "+7($1)$2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{4})/", "+7($1)$2 $3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{4})/", "+7($1)$2-$3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{2})(\d{2})/", "+7($1)$2 $3 $4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{2})(\d{2})/", "+7($1)$2 $3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{2})(\d{2})/", "+7($1)$2-$3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{7})/", "+7 ($1)$2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{7})/", "+7 ($1) $2", $phone);?></li>


                </ul>
            </div>
            <div>
                <ul>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{4})/", "+7 ($1) $2 $3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{4})/", "+7 ($1) $2-$3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{2})(\d{2})/", "+7 ($1) $2 $3 $4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{2})(\d{2})/", "+7 ($1) $2 $3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{2})(\d{2})/", "+7 ($1) $2-$3-$4", $phone);?></li>
                    <li><?=preg_replace('/^7/', '8', $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{7})/", "8 $1$2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{7})/", "8-$1$2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{7})/", "8 $1 $2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{7})/", "8-$1-$2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{4})/", "8 $1 $2 $3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{4})/", "8 $1 $2-$3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{4})/", "8 $1-$2-$3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{4})/", "8-$1-$2-$3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{2})(\d{2})/", "8 $1 $2 $3 $4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{2})(\d{2})/", "8 $1 $2 $3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{2})(\d{2})/", "8 $1 $2-$3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{2})(\d{2})/", "8 $1-$2-$3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{2})(\d{2})/", "8-$1-$2-$3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{7})/", "8($1)$2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{4})/", "8($1)$2 $3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{4})/", "8($1)$2-$3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{2})(\d{2})/", "8($1)$2 $3 $4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{2})(\d{2})/", "8($1)$2 $3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{2})(\d{2})/", "8($1)$2-$3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{7})/", "8 ($1)$2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{7})/", "8 ($1) $2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{4})/", "8 ($1) $2 $3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{4})/", "8 ($1) $2-$3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{2})(\d{2})/", "8 ($1) $2 $3 $4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{2})(\d{2})/", "8 ($1) $2 $3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{3})(\d{3})(\d{2})(\d{2})/", "8 ($1) $2-$3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{6})/", "7 $1$2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{6})/", "7-$1$2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{6})/", "7 $1 $2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{6})/", "7-$1-$2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{4})/", "7 $1 $2 $3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{4})/", "7 $1 $2-$3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{4})/", "7 $1-$2-$3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{4})/", "7-$1-$2-$3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{2})(\d{2})/", "7 $1 $2 $3 $4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{2})(\d{2})/", "7 $1 $2 $3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{2})(\d{2})/", "7 $1 $2-$3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{2})(\d{2})/", "7 $1-$2-$3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{2})(\d{2})/", "7-$1-$2-$3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{6})/", "7($1)$2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{4})/", "7($1)$2 $3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{4})/", "7($1)$2-$3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{2})(\d{2})/", "7($1)$2 $3 $4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{2})(\d{2})/", "7($1)$2 $3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{2})(\d{2})/", "7($1)$2-$3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{6})/", "7 ($1)$2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{6})/", "7 ($1) $2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{4})/", "7 ($1) $2 $3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{4})/", "7 ($1) $2-$3", $phone);?></li>
                </ul>

            </div>
            <div>
                <ul>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{2})(\d{2})/", "7 ($1) $2 $3 $4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{2})(\d{2})/", "7 ($1) $2 $3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{2})(\d{2})/", "7 ($1) $2-$3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{6})/", "+7 $1$2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{6})/", "+7-$1$2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{6})/", "+7 $1 $2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{6})/", "+7-$1-$2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{4})/", "+7 $1 $2 $3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{4})/", "+7 $1 $2-$3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{4})/", "+7 $1-$2-$3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{4})/", "+7-$1-$2-$3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{2})(\d{2})/", "+7 $1 $2 $3 $4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{2})(\d{2})/", "+7 $1 $2 $3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{2})(\d{2})/", "+7 $1 $2-$3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{2})(\d{2})/", "+7 $1-$2-$3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{2})(\d{2})/", "+7-$1-$2-$3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{6})/", "+7($1)$2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{4})/", "+7($1)$2 $3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{4})/", "+7($1)$2-$3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{2})(\d{2})/", "+7($1)$2 $3 $4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{2})(\d{2})/", "+7($1)$2 $3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{2})(\d{2})/", "+7($1)$2-$3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{6})/", "+7 ($1)$2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{6})/", "+7 ($1) $2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{4})/", "+7 ($1) $2 $3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{4})/", "+7 ($1) $2-$3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{2})(\d{2})/", "+7 ($1) $2 $3 $4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{2})(\d{2})/", "+7 ($1) $2 $3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{2})(\d{2})/", "+7 ($1) $2-$3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{6})/", "8 $1$2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{6})/", "8-$1$2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{6})/", "8 $1 $2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{6})/", "8-$1-$2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{4})/", "8 $1 $2 $3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{4})/", "8 $1 $2-$3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{4})/", "8 $1-$2-$3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{4})/", "8-$1-$2-$3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{2})(\d{2})/", "8 $1 $2 $3 $4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{2})(\d{2})/", "8 $1 $2 $3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{2})(\d{2})/", "8 $1 $2-$3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{2})(\d{2})/", "8 $1-$2-$3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{2})(\d{2})/", "8-$1-$2-$3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{6})/", "8($1)$2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{4})/", "8($1)$2 $3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{4})/", "8($1)$2-$3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{2})(\d{2})/", "8($1)$2 $3 $4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{2})(\d{2})/", "8($1)$2 $3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{2})(\d{2})/", "8($1)$2-$3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{6})/", "8 ($1)$2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{6})/", "8 ($1) $2", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{4})/", "8 ($1) $2 $3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{4})/", "8 ($1) $2-$3", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{2})(\d{2})/", "8 ($1) $2 $3 $4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{2})(\d{2})/", "8 ($1) $2 $3-$4", $phone);?></li>
                    <li><?=preg_replace("/7(\d{4})(\d{2})(\d{2})(\d{2})/", "8 ($1) $2-$3-$4", $phone);?></li>
                </ul>
            </div>
        </div>

    </div>
</div>
<br/>
