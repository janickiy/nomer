<?php
/* @var $this \yii\web\View */

use yii\helpers\Url;

$this->title = \Yii::$app->name.' - получение бесплатных проверок';

/* @var $user \app\models\User */
//$user = \Yii::$app->getUser()->getIdentity();

?>

<div class="registration">
    <div class="clfix">
        <h2>Получение бесплатных проверок</h2>

        <?php if(!$user->is_confirm): ?>
            <p>Для получения бесплатных 5 проверок, вам нужно подтвердить e-mail адрес.</p>
        <?php else: ?>
            <p>Ваш E-mail адрес успешно подтвержден</p>
        <?php endif; ?>

        <?php if($user->is_test): ?>
            <p>К сожалению вы уже получили 5 бесплатных проверок</p>
        <?php else: ?>
            <div class="fLine">
                <input type="button" class="button" id="getFree" value="Получить 5 проверок" <?=(!$user->is_confirm)?"disabled":"";?>>
                <?php if(!$user->is_confirm): ?>
                    <input type="button" class="button" id="confirmEmail" value="Подтвердить e-mail">
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if(false):?><script language="JavaScript"><?php endif; ?>

<?php ob_start(); ?>

    $("#confirmEmail").on("click", function() {
        var self = $(this);
        self.attr("disabled", "disabled");
        self.css("cursor", "wait");
        $.getJSON("<?=Url::toRoute(["site/send-confirm"]);?>", {}, function() {
            self.val("Письмо отправлено");
            setTimeout(function() {
                self.removeAttr("disabled").val("Отправить письмо ещё раз");
                self.css("cursor", "pointer");
            }, 60000);
        })
    });

    $("#getFree").on("click", function() {
        var self = $(this);
        self.attr("disabled", "disabled");
        self.css("cursor", "wait");
        $.getJSON("<?=Url::toRoute(["site/free"]);?>", {}, function(response) {
            self.val("Проверки начислены");
            if(response.success) {
                $('.tarif span').html(response.checks);
            }
            self.css("cursor", "pointer");
        })
    });

<?php $js = ob_get_contents(); ob_end_clean(); $this->registerJs($js); ?>