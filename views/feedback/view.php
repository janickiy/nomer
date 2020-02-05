<?php
/* @var $this \yii\web\View */
/* @var $ticket \app\models\Ticket */
/* @var $ticketsDataProvider \yii\data\ActiveDataProvider */
/* @var $comments \yii\data\ActiveDataProvider */

use app\models\Ticket;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

if(in_array($ticket->status, [6, 8])) {
    $ticket->status = 1;
}

$this->title = \Yii::$app->name.' - обратная связь';
?>
<div class="breadcrumbs">
    <ul class="breadcrumb">
        <?php if(\Yii::$app->devicedetect->isMobile() || \Yii::$app->devicedetect->isTablet()): ?>
            <li><?= Html::a('Главная', Url::home()) ?></li>
            <li><?=Html::a("Обратная связь", ["feedback/index"]);?></li>
            <li>Запрос #<?=$ticket->id;?></li>
        <?php else: ?>
            <li><?= Html::a('Главная', Url::home()) ?></li>
            <li><?=Html::a("Обратная связь", ["feedback/index"]);?></li>
            <li>Запрос #<?=$ticket->id;?></li>
        <?php endif; ?>
    </ul>
</div>

<div class="page-content">
    <div class="cont clfix">
        <h1>Обратная связь / Запрос #<?=$ticket->id;?></h1>

        <table class="tickets">
            <tr>
                <th>Раздел</th>
                <th>Дата</th>
                <th>Тема</th>
                <th>Статус</th>
            </tr>
            <tr>
                <td><?=ArrayHelper::getValue(Ticket::SUBJECTS, $ticket->subject_id);?></td>
                <td><?=$ticket->tm_create;?></td>
                <td><?=$ticket->subject;?></td>
                <td><?=ArrayHelper::getValue(Ticket::STATUSES, $ticket->status);?></td>
            </tr>
        </table>

        <br />

        <div class="clientticketreplyheader">
            <table>
                <tr><td style="padding: 5px; font-size: 13px;"><strong style="font-weight: bold"><?=$ticket->user->email;?></strong><br>Клиент</td><td></td></tr>
            </table>
        </div>
        <div class="clientticketreply">
            <?=nl2br($ticket->text);?>
        </div>

        <br />
        <?php foreach ($comments as $c): ?>
            <?php if(ArrayHelper::getValue($c, "user.is_admin", false)): ?>
                <div class="adminticketreplyheader">
                    <table style="width: 100%">
                        <tr><td style="padding: 5px; font-size: 13px;"><strong style="font-weight: bold">Администратор</strong></td><td style="padding: 5px; font-size: 13px; text-align: right;"><?=$c->tm_create;?></td></tr>
                    </table>
                </div>
                <div class="adminticketreply">
                    <?=nl2br($c->text);?>
                </div>
            <?php else: ?>
                <div class="clientticketreplyheader">
                    <table style="width: 100%">
                        <tr><td style="padding: 5px; font-size: 13px;"><strong style="font-weight: bold"><?=$ticket->user->email;?></strong><br>Клиент</td><td style="padding: 5px; font-size: 13px; text-align: right;"><?=$c->tm_create;?></td></tr>
                    </table>
                </div>
                <div class="clientticketreply">
                    <?=nl2br($c->text);?>
                </div>
            <?php endif; ?>
                <br />
        <?php endforeach; ?>

        <?php if($ticket->status != 4): ?>
            <?=Html::a("Если проблема решена, нажмите для закрытия тикета", ["feedback/close", "id" => $ticket->id], ["class" => "closeticket"]);?>
            <br /><br />
            <?php $form = ActiveForm::begin([
                "action" => ["feedback/comment", "id" => $ticket->id]
            ]); ?>
            <?= $form->field($comment, "text")->textarea(["rows" => 10]); ?>
            <input value="Добавить" type="submit" class="ticket">
            <?php ActiveForm::end(); ?>
        <?php else: ?>
            <?=Html::a("Переоткрыть тикет", ["feedback/reopen", "id" => $ticket->id], ["class" => "closeticket"]);?>
        <?php endif; ?>


    </div>
</div>
