<?php
/* @var $this \yii\web\View */
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = "Счет успешно пополнен";

$payment = \app\models\Payment::find()->where(["user_id" => \Yii::$app->getUser()->getId()])->orderBy(["id" => SORT_DESC])->one();
?>

    <div class="registration">
        <div class="cont clfix">
            <h2>Счет успешно пополнен</h2>

            <button type="button" onclick="location.href='/'" class="inpBtn payBtn">Начать поиск</button>
        </div>
    </div>

<?php if($payment): ?>
<script>
    ga('require', 'ecommerce');
    ga('ecommerce:addTransaction', {
        'id': '<?=$payment->id;?>',                     // Transaction ID. Required.
        'affiliation': '<?=\Yii::$app->name;?>',   // Affiliation or store name.
        'revenue': '<?=$payment->sum;?>',               // Grand Total.
        'shipping': '5',                  // Shipping.
        'tax': '0'                     // Tax.
    });
    ga('ecommerce:addItem', {
        'id': '<?=$payment->id;?>',
        'name': 'Пополнение на <?=$payment->sum;?> руб.',
        'sku': '<?=$payment->sum;?>',
        'category': 'balance',
        'price': '<?=$payment->sum;?>',
        'quantity': '1',
        'currency': 'RUB' // local currency code.
    });
    ga('ecommerce:send');
    </script>
<?php endif; ?>