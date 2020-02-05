<?php
/* @var $this \yii\web\View */
/* @var $model \app\models\Ticket */
/* @var $ticketsDataProvider \yii\data\ActiveDataProvider */

use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = \Yii::$app->name.' - обратная связь';
?>
<div class="breadcrumbs">
    <ul class="breadcrumb">
        <?php if(\Yii::$app->devicedetect->isMobile() || \Yii::$app->devicedetect->isTablet()): ?>
            <li><?= Html::a('Главная', Url::home()) ?></li>
            <li>Обратная связь</li>
        <?php else: ?>
            <li><?= Html::a('Главная', Url::home()) ?></li>
            <li>Обратная связь</li>
        <?php endif; ?>
    </ul>
</div>

<div class="page-content">
    <div class="cont clfix">
        <h1>Обратная связь</h1>

        <?php if(\Yii::$app->getUser()->isGuest): ?>
            <h2>Что бы с нами связаться, вам нужно авторизоваться!</h2>
            <p align="center"><a href="#signup" class="button" style="width: 300px">Войти</a></p>
        <?php else: ?>
            <?php $form = ActiveForm::begin([
                "enableAjaxValidation" => false,
                "enableClientValidation" => false,
            ]); ?>
            <?=$form->field($model, "subject_id", ['template' => '{input}'] )->dropDownList(\app\models\Ticket::SUBJECTS, ['class' => 'inp']); ?>
            <?= $form->field($model, "text", ["template" => "{input}"])->textarea(["class" => "inp", "placeholder" => $model->getAttributeLabel("text")]); ?>
            <?= $form->field($model, 'reCaptcha', ["template" => "{input}"])->widget(\himiklab\yii2\recaptcha\ReCaptcha::className()) ?>
            <input class="button" value="Создать запрос" type="submit" onclick="$(this).attr('disabled', 'disabled'); submit();">
            <?php ActiveForm::end(); ?>

            <?php if($ticketsDataProvider->getTotalCount()): ?>
                <?=\yii\grid\GridView::widget([
                    "dataProvider" => $ticketsDataProvider,
                    "columns" => [
                        "id",
                        "tm_create",
                        "subject_id",
                        "text",
                        "status",
                        [
                            'class' => ActionColumn::className(),
                            'template' => '{view} {close} {reopen}',
                            'buttons' => [
                                'view' => function ($url, $model, $key) {
                                    return $model->is_demo ? Html::a('Выключить demo', ['set-demo', 'id' => $model->id], ['class' => 'btn btn-danger']) : Html::a('Включить demo', ['set-demo', 'id' => $model->id], ['class' => 'btn btn-success']);
                                }
                            ]
                        ]
                    ]
                ]); ?>
            <?php endif; ?>
        <?php endif;?>

        <?php if($success = \Yii::$app->session->getFlash("success", false)): ?>
            <p><?=$success;?></p>
        <?php else: ?>

        <?php endif; ?>
    </div>
</div>
