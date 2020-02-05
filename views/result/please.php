<?php
/* @var $this \yii\web\View */
?>
<div class="page-content">
    <div class="cont clfix">
        <p class="search-info"><img src="/img/pay/payments_info.png"><br>Мы доказали, что можем найти для Вас<br>информацию по номеру телефона.</p>

    </div>
    <div class="line"></div>
    <div class="cont clfix">
        <p class="we-can-search">Мы можем выполнить полноценный поиск данных,<br><?=\Yii::$app->getUser()->isGuest?"пожалуйста, зарегистрируйтесь":"пополните пожалуйства Ваш баланс";?>.</p>
        <div style="text-align: center">
        <?php if(\Yii::$app->getUser()->isGuest): ?>
            <input class="button" value="Перейти к регистрации" type="button" onclick="location.href='#signup'" style="display: inline;">
        <?php else: ?>
            <input class="button" value="Купить проверки" type="button" onclick="location.href='/pay'" style="display: inline;">
        <?php endif; ?>
        </div>
    </div>
</div>

<pre style="color: #FFFFFF">
    IP: <?=\Yii::$app->request->getUserIP();?>
    PHONE: <?=$phone;?>
    TM: <?=date("Y-m-d H:i:s");?>
</pre>