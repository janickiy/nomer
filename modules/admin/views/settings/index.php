<?php

/* @var $this \yii\web\View */
/* @var $tab string */
/* @var $model \yii\db\ActiveRecord */
/* @var $dataProvider \yii\data\ActiveDataProvider */

use yii\bootstrap\Tabs;
use yii\helpers\Url;

$this->title = 'Настройки';

echo Tabs::widget([
    'id' => 'SettingsTabsWidget',
    'renderTabContent' => false,
    'items' => [
        [
            'label' => 'Индексы поиска',
            'url' => Url::toRoute(['settings/index', 'tab' => 'index']),
            'active' => $tab == 'index'
        ],
        [
            'label' => 'Заблокированные пользователи',
            'url' => Url::toRoute(['settings/index', 'tab' => 'bans']),
            'active' => $tab == 'bans'
        ],
        [
            'label' => 'Домены',
            'url' => Url::toRoute(['settings/index', 'tab' => 'domains']),
            'active' => $tab == 'domains'
        ],
        [
            'label' => 'Статистика по фингерпринтам',
            'url' => Url::toRoute(['settings/index', 'tab' => 'fingerprints']),
            'active' => $tab == 'fingerprints'
        ],
        [
            'label' => 'Заблокированные номера',
            'url' => Url::toRoute(['settings/index', 'tab' => 'blocked-phones']),
            'active' => $tab == 'blocked-phones'
        ]
    ]
]);

echo '<div class="tab-content">';

switch ($tab) {
    case 'index': echo $this->render('_search_index'); break;
    case 'bans': echo $this->render('_bans', compact('dataProvider')); break;
    case 'domains': echo $this->render('_domains', compact('model', 'dataProvider')); break;
    case 'fingerprints': echo $this->render('_fingerprints', compact('dataProvider')); break;
    case 'blocked-phones': echo $this->render('_blocked_phones', compact('phones')); break;
}

echo '</div>';
