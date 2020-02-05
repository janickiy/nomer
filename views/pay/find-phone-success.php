<?php
/* @var $this \yii\web\View */
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = "Оплата прошла успешно";

$payment = \app\models\Payment::find()->where(["user_id" => \Yii::$app->getUser()->getId()])->orderBy(["id" => SORT_DESC])->one();
?>

<div class="page-content">
    <div class="cont clfix">
        <h1><?=$this->title;?></h1>

        <h2>Мы начали поиск, он займет от 10 минут до 1 часа. Результаты будут доступны в <a href="<?=Url::toRoute(["tickets/index"]);?>">Тикет-системе</a></h2>

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