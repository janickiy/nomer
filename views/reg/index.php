<?php

/* @var $this yii\web\View */
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\MaskedInput;

$this->title = \Yii::$app->name;
?>
<div class="row" id="phone" style="margin-bottom: 15px;">
    <div class="col-md-offset-4 col-md-4 col-xs-12">
        <form>
            <div class="form-group">
                <?=MaskedInput::widget([
                    'name' => 'phone',
                    'mask' => '[+7] (999) 999-99-99',
                    'options' => [
                        'class' => 'form-control',
                        'style' => 'text-align: center; padding: 5px;',
                        'placeholder' => 'Введите номер телефона'
                    ]
                ]);?>
            </div>

            <button class="btn btn-success form-control" type="button" id="regButton" data-loading-text="Код отправлен...">Получить код</button>

            <?=Html::button("У меня уже есть код", [
                    "class" => "btn btn-warning form-control",
                    "style" => "margin-top: 5px",
                    "onclick" => "$('#sms').show();",
                    "type" => "button"
                ]);?>
        </form>
    </div>
</div>

<div class="row" style="display: none;" id="sms">
    <div class="col-md-offset-4 col-md-4 col-xs-12">
        <form>
            <div class="form-group">
                <?=Html::textInput("code", "",
                    [
                        'class' => 'form-control',
                        'style' => 'text-align: center; padding: 5px;',
                        'placeholder' => 'Введите код доступа'
                    ]
                );?>
            </div>

            <button class="btn btn-primary form-control" type="button" id="checkButton" data-loading-text="Проверяем код">Войти</button>
        </form>
    </div>
</div>

<?php if(false): ?><script><?php endif; ?>
    <?php ob_start(); ?>

    $('#regButton').bind("click", function() {
        var $btn = $(this).button('loading');
        var phone = $("input[name=phone]").val();
        $.getJSON("<?=Url::toRoute(["reg/sms"]);?>", { phone: phone }, function(response) {
            if(response.error == 0) {
                $('#phone').hide();
                $('#sms').show();
            }
            $btn.reset();
        });
    });

    $('#checkButton').bind("click", function() {
        var $btn = $(this).button('loading');
        var code = $("input[name=code]").val();
        $.getJSON("<?=Url::toRoute(["reg/check"]);?>", { code: code }, function(response) {
            if(response.error == 0) {
                location.href = "/";
            } else {
                $btn.reset();
            }
        });
    });

    <?php $js = ob_get_contents(); ob_end_clean(); $this->registerJs($js); ?>
