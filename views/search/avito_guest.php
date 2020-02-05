<?php

/* @var $this \yii\web\View */
/* @var $result array */
/* @var $link string */
/* @var $phone int */

use yii\helpers\Html;
use yii\helpers\Url;

?>

<?php if(count($result)): ?>
    <?php
    krsort($result);

    $earliestDate = new DateTime();
    $sumPrice = 0;

    foreach ($result as $data) {
        $date = new DateTime($data['time']);

        if ($date->getTimestamp() < $earliestDate->getTimestamp()) {
            $earliestDate = $date;
        }

        $sumPrice += $data['price'];
    }

    $avgPrice = count($result) == 0 ? 0 : round($sumPrice / count($result));



    ?>
    <table class="rTable">
        <?php foreach(array_slice($result, 0, 3) as $item): ?>
            <?php
            $names = preg_split("/ /", $item["title"]);
            $xnames = [];
            foreach ($names as $name) {
                if(mb_strlen($name) > 2) {
                    $xnames[] = mb_substr($name, 0, 1) . str_repeat("*", mb_strlen($name) - 2) . mb_substr($name, -1);
                } else {
                    $xnames[] = $name;
                }
            }
            $item["title"] = join(" ", $xnames);
            ?>
            <tr>
                <td><span class="rDate"><?=Yii::$app->formatter->asDatetime($item["time"], "dd.MM.yyyy");?> |</span> <span class="rPrice"><?=preg_replace("/\d/", "*", \Yii::$app->formatter->asDecimal($item["price"]));?> р.</span></td>
                <td><?=$item["title"];?></td>
            </tr>
        <?php endforeach; ?>
        <tr class="rResult">
            <?php if(count($result) > 3): ?>
                <td colspan="2" class="rRes">Ещё найдено <?= Yii::t('app', '{n,plural,=1{<strong>одно</strong> объявление} few{<strong>#</strong> объявления} many{<strong>#</strong> объявлений} other{<strong>#</strong> объявления}}', ['n' => count($result)-3]) ?> с этим телефоном, покажем после регистрации</td>
            <?php endif; ?>
        </tr>
    </table>
    <div class="sinfo">
        Если хотите увидеть все объявления - зарегистрируйтесь.
        <span class="btns"><a href="#signup" class="buy">Регистрация / Вход</a></span>
    </div>
<?php else: ?>
    <p class="no">Ничего не найдено</p>
<?php endif; ?>
