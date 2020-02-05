<?php

/* @var $this \yii\web\View */
/* @var $ticket \app\models\Ticket */
/* @var $comment \app\models\TicketComment */
/* @var $comments \yii\data\ActiveDataProvider */
/* @var $replies \app\models\TicketReply[] */

use \app\models\Ticket;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Запрос #' . $ticket->id;

$user = \app\models\User::find()->where(["id" => $ticket->user_id])->one();

?>

<div class="row">
    <div class="col-md-12">
        <div class="portlet light ">
            <div class="portlet-title tabbable-line">
                <div class="caption caption-md">
                    <i class="icon-globe theme-font hide"></i>
                    <span class="caption-subject font-blue-madison bold uppercase">Запрос #<?= $ticket->id ?></span>
                </div>
            </div>
            <div class="portlet-body">
                <table class="tickets">
                    <tr>
                        <th>Раздел</th>
                        <th>Сайт</th>
                        <th>Дата</th>
                        <th>Тема</th>
                        <th>Статус</th>
                    </tr>
                    <tr>
                        <td><?=ArrayHelper::getValue(Ticket::SUBJECTS, $ticket->subject_id);?></td>
                        <td><?=ArrayHelper::getValue($ticket, 'site.name');?></td>
                        <td><?=$ticket->tm_create;?></td>
                        <td><?=$ticket->subject;?></td>
                        <td><?=ArrayHelper::getValue(Ticket::STATUSES, $ticket->status);?></td>
                    </tr>
                    <?php if(trim($ticket->url) != ""): ?>
                    <tr>
                        <td colspan="5"><?=$ticket->url;?></td>
                    </tr>
                    <?php endif; ?>
                </table>

                <br />

                <div class="clientticketreplyheader">
                    <table>
                        <tr><td style="padding: 5px; font-size: 13px;">
                                <?php if($ticket->user): ?>
                                    <?php $email = $ticket->user->email;?>
                                <strong style="font-weight: bold"><?=Html::a($email?$email:"iOS пользователь", ["users/view", "id" => $ticket->user_id]);?></strong><?=$ticket->user->comment?", ".$ticket->user->comment:"";?><br>Клиент, <?=$ticket->user->ip;?>
                                    <?php else: ?>
                                    <strong>iOS</strong>
                                <?php endif;?>
                            </td><td><?=$ticket->is_payed?Html::tag("b", "ОПЛАЧЕНО"):"";?></td></tr>
                    </table>
                </div>
                <div class="clientticketreply">
                    <?=nl2br($ticket->text);?>
                </div>

                <br />
                <?php foreach ($comments->getModels() as $c): ?>
                    <?php if(ArrayHelper::getValue($c, 'user.is_admin', false)): ?>
                        <div class="adminticketreplyheader">
                            <table style="width: 100%">
                                <tr><td style="padding: 5px; font-size: 13px;"><strong style="font-weight: bold">Администратор</strong></td><td style="padding: 5px; font-size: 13px; text-align: right;"><?=$c->tm_create;?></td></tr>
                            </table>
                        </div>
                        <div class="adminticketreply">
                            <?=nl2br($c->text);?>
                            <p style="margin-top: 5px; margin-bottom: 0; text-align: right; font-size: 10px;"><?=Html::a("Удалить", ["tickets/comment-delete", "id" => $c->id]);?></p>
                        </div>
                    <?php else: ?>
                        <div class="clientticketreplyheader">
                            <table style="width: 100%">
                                <tr><td style="padding: 5px; font-size: 13px;"><strong style="font-weight: bold"><?=Html::a($ticket->user->email, ["users/view", "id" => $ticket->user_id]);?></strong><br>Клиент</td><td style="padding: 5px; font-size: 13px; text-align: right;"><?=$c->tm_create;?></td></tr>
                            </table>
                        </div>
                        <div class="clientticketreply">
                            <?=nl2br($c->text);?>
                            <p style="margin-top: 5px; margin-bottom: 0; text-align: right; font-size: 10px;"><?=Html::a("Удалить", ["tickets/comment-delete", "id" => $c->id]);?></p>
                        </div>
                    <?php endif; ?>
                    <br />
                <?php endforeach; ?>

                <?php if($ticket->status != 4): ?>
                    <?=Html::a("Задача на разработку", ["tickets/develop", "id" => $ticket->id], ["class" => "btn btn-primary"]);?>
                    <?=Html::a("Игнорировать тикет", ["tickets/ignore", "id" => $ticket->id], ["class" => "btn btn-warning"]);?>
                    <?=Html::a("Закрыть тикет", ["tickets/close", "id" => $ticket->id], ["class" => "btn btn-warning"]);?>
                    <?=Html::a("Удалить тикет", ["tickets/delete", "id" => $ticket->id], ["class" => "btn btn-danger"]);?>
                    <br /><br />
                    <div class="row">
                        <div class="col-md-6">
                            <?php $form = ActiveForm::begin([
                                "action" => ["tickets/comment", "id" => $ticket->id]
                            ]); ?>
                            <?= $form->field($comment, "text")->textarea(["rows" => 10]); ?>
                            <input value="Добавить" type="submit" class="ticket">
                            <?php ActiveForm::end(); ?>
                        </div>
                        <div class="col-md-6">
                            <label class="control-label">Быстрые ответы</label>
                            <ul class="list-group">
                                <?php foreach ($replies as $reply): ?>
                                    <li class="list-group-item">
                                        <div class="btn-group" style="width: 100%;">
                                            <button data-action="send" data-text="<?=$reply->text;?>" type="button" class="btn btn-primary col-md-3"><i class="fa fa-send"></i> Отправить</button>
                                            <button data-action="copy" data-text="<?=$reply->text;?>" type="button" class="btn btn-default col-md-9" style="overflow: hidden; text-overflow: ellipsis"><?=$reply->text;?></button>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                                <li class="list-group-item" id="newReply">
                                    <div class="input-group">
                                        <input class="form-control" id="replyText" placeholder="Введите быстрый ответ" type="text">
                                        <span class="input-group-btn">
                                            <button class="btn red" id="replyAdd" type="button">Добавить</button>
                                        </span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                <?php else: ?>
                    <?=Html::a("Переоткрыть", ["tickets/reopen", "id" => $ticket->id], ["class" => "btn btn-warning"]);?>
                    <?=Html::a("Удалить тикет", ["tickets/delete", "id" => $ticket->id], ["class" => "btn btn-danger"]);?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if(false):?><script><?php endif; ?>
    <?php ob_start(); ?>

    $('#replyAdd').click(function() {
        var text = $('#replyText').val();
        if(text.trim() === '') {
            return false;
        }
        $.post('<?=Url::toRoute(["tickets/add-reply", "id" => $ticket->id]);?>', {
            text: text
        }, function(response) {
            if(response !== '') {
                $(response).insertBefore('#newReply');
            }
        }, 'html');
    });

    $( document ).on( "click", "[data-action=\"send\"]", function() {
        var text = $(this).data("text");
        $('#<?=Html::getInputId($comment, "text");?>').val(text);
        $('input.ticket').click();
    });

    $( document ).on( "click", "[data-action=\"copy\"]", function() {
        var text = $(this).data("text");
        $('#<?=Html::getInputId($comment, "text");?>').val(text);
    });

    <?php $js = ob_get_contents(); ob_end_clean(); $this->registerJs($js); ?>
