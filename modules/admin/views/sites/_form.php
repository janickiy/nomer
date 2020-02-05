<?php

/* @var $this \yii\web\View */
/* @var $model \app\models\Site */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<?php $form = ActiveForm::begin();?>
<?= $form->field($model, 'name')->textInput() ?>
<?= $form->field($model, 'comment')->textInput() ?>
<?= $form->field($model, 'is_demo')->checkbox() ?>
<?= $form->field($model, 'type_id')->dropDownList(["1" => "Обычный", 2 => "Новый тип поиска"]) ?>
<?= $form->field($model, 'phone')->textInput()->hint("Номер телефон для QIWI кошелька. Пароль должен быть Ag6K2oxG. Нужно отключать подтверждение по смс. По паспортным данных должен находится ИНН на nalog.ru") ?>
<?= $form->field($model, 'yandex_money_account')->textInput()->hint("№ кошелька ЯД.  По паспортным данных должен находится ИНН на nalog.ru. Настройки -> Все остальное -> Уведомления. Указать http://yamoney.pmadm.ru/pay/result + поставить галочку.") ?>
<?= $form->field($model, 'vk_id')->textInput()->hint("Создаем приложение в контакте. Название: ДОМЕН. Тип: веб сайт. Адрес: https://ДОМЕН. Домены: ДОМЕН. Сюда берем ID приложения.") ?>
<?= $form->field($model, 'vk_secret')->textInput()->hint("Сюда берем Защищенный ключ") ?>
<?= $form->field($model, 'fb_id')->textInput()->hint("Создаем приложение на facebook. Отображаемое имя: ДОМЕН. Настройки -> Основное: Домены приложений: ДОМЕН. Добавляем платформу Веб сайт с адресом: https://ДОМЕН. Отсюда нам нужен Индентификатор приложения") ?>
<?= $form->field($model, 'fb_secret')->textInput()->hint("Сюда берем Секрет приложения. Проверка приложения -> Сделать приложение доступным для всех.") ?>
<?= $form->field($model, 'gg_id')->textInput()->hint("https://console.developers.google.com , Создаем проект с названием ДОМЕН (точку заменяем на -). Диспетчер API -> Учетные данные. Окно запроса доступа OAuth. Название продукта: ДОМЕН. Учетные данные -> Создать идентификатор клиента OAuth. Веб приложение. Название: ДОМЕН. Разрешенные источники: https://ДОМЕН. Разрешенные URL перенаправления: https://ДОМЕН/auth?authclient=google . Сюда берем идентификатор клиента без пробелов.") ?>
<?= $form->field($model, 'gg_secret')->textInput()->hint("Сюда берем секрет клиента ьез пробелов") ?>
<?= $form->field($model, 'platiru_id')->textInput()->hint("№ товара в магазине plati.ru") ?>

<?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => 'btn btn-success']) ?>
<?php ActiveForm::end(); ?>
