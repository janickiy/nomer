<?php

/* @var $this \yii\web\View */
/* @var $model \app\models\Wallet */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Site;
use app\models\Wallet;

$form = ActiveForm::begin(['method' => 'POST']);
echo $form->field($model, 'type_id')->dropDownList(Wallet::getWalletTypes());
echo $form->field($model, 'site_id')->dropDownList(ArrayHelper::map(Site::find()->all(), 'id', 'name'), ['prompt' => 'Нет']);
echo $form->field($model, 'wallet_id');
echo $form->field($model, 'login');
echo $form->field($model, 'password');
echo $form->field($model, 'phone');
echo $form->field($model, 'status')->checkbox();
echo $form->field($model, 'comment')->textarea();
echo Html::submitButton('Сохранить', ['class' => 'btn btn-success']);
$form::end();
