<?php
/* @var $this \yii\web\View */
/* @var $model \app\models\forms\BlockForm */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\MaskedInput;
use \yii\widgets\ActiveForm;

$this->title = \Yii::$app->name . ' - исключение номера из базы';
?>



<div class="breadcrumbs">
    <ul class="breadcrumb">
        <?php if(\Yii::$app->devicedetect->isMobile() || \Yii::$app->devicedetect->isTablet()): ?>
            <li><?= Html::a('Главная', Url::home()) ?></li>
            <li>Исключение номера из базы</li>
        <?php else: ?>
            <li><?= Html::a('Главная', Url::home()) ?></li>
            <li>Исключение номера из базы</li>
        <?php endif; ?>
    </ul>
</div>


<div class="page-content">
    <div class="cont clfix">

        <h1>Исключение номера из базы</h1>

        <p class="danger">Исключение номера из базы БЕСПЛАТНОЕ!</p>

        <?php if($phone): ?>
            <p class="qiwi-descr"><span>Введенный номер уже заблокирован!</span></p>
        <?php endif; ?>

        <div style="margin: 0 auto; width: 335px;">
        <?php $form = ActiveForm::begin() ?>
        <?=$form->field($model, "phone", ["template" => "{input}{error}"])->widget(MaskedInput::className(), [
            'mask' => '+7 (999) 999-99-99',
            'options' => [
                'class' => 'searchPhone searchPhoneInner',
                'placeholder' => '+7 (___) ___-__-__',
            ]
        ]);?>

        <br>

        <?=$form->field($model, 'reCaptcha', ['template' => '{input}{error}'])->widget(\himiklab\yii2\recaptcha\ReCaptcha::className()) ?>
        <br>
        <input class="button" value="Исключить" type="submit" style="width: 100%;">
        <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php $this->registerJs("jQuery('[name=phone]').bind('paste', function(e){
    var text = (e.originalEvent || e).clipboardData.getData('text/plain');
    text = text.replace(/[^0-9]/gim, '');
    if( text.charAt( 0 ) === '7' || text.charAt( 0 ) === '8' )
        text = text.slice( 1 );
    $(this).val(text);
 });"); ?>

