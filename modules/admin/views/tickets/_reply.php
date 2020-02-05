<?php
/* @var $this \yii\web\View */
/* @var $reply \app\models\TicketReply */
?>
<li class="list-group-item">
    <div class="btn-group" style="width: 100%;">
        <button data-action="send" data-text="<?=$reply->text;?>" type="button" class="btn btn-primary col-md-3"><i class="fa fa-send"></i> Отправить</button>
        <button data-action="copy" data-text="<?=$reply->text;?>" type="button" class="btn btn-default col-md-9"><?=$reply->text;?></button>
    </div>
</li>