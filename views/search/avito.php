<?php

/* @var $this \yii\web\View */
/* @var $result array */
/* @var $link string */
/* @var $phone int */
/* @var $searchRequest \app\models\SearchRequest */

use yii\helpers\Html;
use yii\helpers\Url;

?>

<?php if(is_array($result) && count($result)): ?>
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
            $item["price"] = \Yii::$app->formatter->asDecimal($item["price"]);
            if(!isset($cache) && (!$searchRequest->user_id || !$searchRequest->is_payed)) {
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
                $item["price"] = preg_replace("/\d/", "*", $item["price"]);
            }
            ?>
        <tr>
            <td><span class="rDate"><?=Yii::$app->formatter->asDatetime($item["time"], "dd.MM.yyyy");?> |</span> <span class="rPrice"><?=$item["price"];?> р.</span></td>
            <td><?=Html::a($item["title"], Url::toRoute(["result/avito", "phone" => preg_replace("/^7/", "8", $phone), "id" => $item["Id"]]), ["target" => "_blank"]);?></td>
        </tr>
        <?php endforeach; ?>

        <?php if(!isset($cache) && (!$searchRequest->user_id || !$searchRequest->is_payed)): ?>
            <?php if(count($result) > 3): ?>
                <tr class="rResult"><td colspan="2" class="rRes">Ещё найдено <?= Yii::t('app', '{n,plural,=1{<strong>одно</strong> объявление} few{<strong>#</strong> объявления} many{<strong>#</strong> объявлений} other{<strong>#</strong> объявления}}', ['n' => count($result)-3]) ?> с этим телефоном, покажем после регистрации</td></tr>
            <?php endif; ?>
        <?php else: ?>
            <tr class="rResult">
                <?php if(count($result) > 3): ?>
                    <td><div class="allRes"><a href="<?=Url::toRoute(["result/avito", "phone" => preg_replace("/^7/", "8", $phone)]);?>">Все результаты: <span><?=count($result);?></span></a></div></td>
                    <td class="rRes">На авито с <strong><?=\Yii::$app->formatter->asDate($earliestDate, "MMMM yyyy")?>г.</strong> Дал <?= Yii::t('app', '{n,plural,=1{<strong>одно</strong> объявление} few{<strong>#</strong> объявления} many{<strong>#</strong> объявлений} other{<strong>#</strong> объявления}}', ['n' => count($result)]) ?> со средним чеком <strong><?=\Yii::$app->formatter->asCurrency($avgPrice, 'RUB');?></strong></td>
                <?php else: ?>
                    <td colspan="2" class="rRes">На авито с <strong><?=\Yii::$app->formatter->asDate($earliestDate, "MMMM yyyy")?>г.</strong> Дал <?= Yii::t('app', '{n,plural,=1{<strong>одно</strong> объявление} few{<strong>#</strong> объявления} many{<strong>#</strong> объявлений} other{<strong>#</strong> объявления}}', ['n' => count($result)]) ?> со средним чеком <strong><?=\Yii::$app->formatter->asCurrency($avgPrice, 'RUB');?></strong></td>
                <?php endif; ?>
            </tr>
        <?php endif; ?>
    </table>
    <?php if(!isset($cache)): ?>
    <?=$this->render("/_parts/_btns", [
        "searchRequest" => $searchRequest,
        "message" => "Если хотите увидеть объявления целиком"
    ]);?>
    <?php endif; ?>
<?php else: ?>
    <p class="no">Ничего не найдено</p>
<?php endif; ?>