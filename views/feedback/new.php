<?php
/* @var $this \yii\web\View */
/* @var $ticket \app\models\Ticket */
/* @var $ticketsDataProvider \yii\data\ActiveDataProvider */

use yii\grid\ActionColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = \Yii::$app->name.' - обратная связь';

$onChange = <<<JS
var subject = document.querySelector('[name="subject"]');
var textarea = document.getElementById('contactform-message');

if (subject.selectedIndex === 1) {
    textarea.value = (textarea.value + '\\n\\nСопсоб пополнения:\\nДата платежа:\\nВремя платежа:').trim();
}
JS;
?>
<div class="breadcrumbs">
    <ul class="breadcrumb">
        <?php if(\Yii::$app->devicedetect->isMobile() || \Yii::$app->devicedetect->isTablet()): ?>
            <li><?= Html::a('Главная', Url::home()) ?></li>
            <li><?= Html::a('Обратная связь', Url::toRoute(["feedback/index"])) ?></li>
            <li>Новый запрос</li>
        <?php else: ?>
            <li><?= Html::a('Главная', Url::home()) ?></li>
            <li><?= Html::a('Обратная связь', Url::toRoute(["feedback/index"])) ?></li>
            <li>Новый запрос</li>
        <?php endif; ?>
    </ul>
</div>

<div class="page-content">
    <div class="cont clfix">
        <h1>Обратная связь / Новый запрос</h1>

            <?php $form = ActiveForm::begin(); ?>
            <?=$form->field($ticket, "subject_id")->dropDownList(\app\models\Ticket::SUBJECTS); ?>
            <?=$form->field($ticket, "subject")->textInput(); ?>
            <?=$form->field($ticket, "text")->textarea(["rows" => 8]); ?>
            <?=$form->field($ticket, 'reCaptcha', ['template' => '{input}{error}'])->widget(\himiklab\yii2\recaptcha\ReCaptcha::className()) ?>
            <input class="ticket" value="Создать запрос" type="submit">
            <?php ActiveForm::end(); ?>

        <?php if($success = \Yii::$app->session->getFlash("success", false)): ?>
            <p><?=$success;?></p>
        <?php else: ?>

        <?php endif; ?>
    </div>
</div>

<?php if(false):?><script><?php endif; ?>
    <?php ob_start(); ?>
    var messageInput = $('#<?=Html::getInputId($ticket, "text");?>');
    $('#<?=Html::getInputId($ticket, "subject_id");?>').on('change', function() {
        if (this.selectedIndex === 1) {
            if(messageInput.val() === '') {
                messageInput.val("Сопсоб пополнения:\nДата платежа:\nВремя платежа:");
            }
        }
        if (this.selectedIndex === 3) {
            if(messageInput.val() === '') {
                $.alert({
                    theme: 'material',
                    title: 'Внимание!',
                    content: 'Сожалеем, но чтобы удалить номер из нашей базы, вам нужно подтвердить владение им. Также предупреждаем вас, что Удаление номера у нас не даст вам анонимности, т.к. мы всю информацию берем из открытых источников и она останется там!',
                });
            }
        }
    });
    <?php $js = ob_get_contents(); ob_end_clean(); $this->registerJs($js); ?>
