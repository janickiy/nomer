<?php

/* @var $this \yii\web\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */

use app\models\ResultCache;
use yii\grid\GridView;
use app\models\SearchRequest;
use app\models\RequestResult;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

$this->title = 'История поиска';

?>

<div class="cont clfix" style="margin-top: 30px">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'tm',
            [
                'format' => 'raw',
                'attribute' => 'phone',
                'value' => function(SearchRequest $model) {
                    $phone = preg_replace('/^7/', '8', $model->phone);
                    return '<a href="/' . $phone . '">' . $phone . '</a>';
                }
            ],
            [
                'header' => 'Оператор',
                'value' => function(SearchRequest $model) {
                    $result = RequestResult::find()->where(['request_id' => $model->id, 'type_id' => ResultCache::TYPE_OPERATOR])->one();
                    if($result) {
                        $operator = Json::decode($result->data);
                        $o = [];
                        $o[] = ArrayHelper::getValue($operator, "operator");
                        $o[] = ArrayHelper::getValue($operator, "region");
                        $o = array_filter($o);
                        return join(", ", $o);
                    }
                    return null;
                }
            ],
            [
                'header' => 'Имена',
                'content' => function(SearchRequest $model) {
                    $names = [];

                    $namesRows = RequestResult::find()->where(["request_id" => $model->id, "type_id" => [
                        ResultCache::TYPE_TRUECALLER,
                        ResultCache::TYPE_NUMBUSTER
                    ]])->all();
                    foreach ($namesRows as $namesRow) {
                        $nameData = Json::decode($namesRow->data);
                        if(!is_null($nameData)) {
                            if(array_key_exists("name", $nameData)) {
                                $names[] = ArrayHelper::getValue($nameData, "name");
                            } else {
                                $names = ArrayHelper::merge($names, ArrayHelper::getColumn($nameData, "name"));
                            }
                        }
                    }

                    $names = array_unique($names);

                    if(count($names) < 2) {
                        $namesRows = RequestResult::find()->where(["request_id" => $model->id, "type_id" => [
                            ResultCache::TYPE_FACEBOOK,
                            ResultCache::TYPE_VK_2012,
                            ResultCache::TYPE_VK_OPEN,
                            ResultCache::TYPE_VIBER,
                            ResultCache::TYPE_TELEGRAM,
                            ResultCache::TYPE_VK,
                            ResultCache::TYPE_AVITO,
                        ]])->all();
                        foreach ($namesRows as $namesRow) {
                            $nameData = Json::decode($namesRow->data);
                            if(in_array($namesRow->type_id, [ResultCache::TYPE_TELEGRAM, ResultCache::TYPE_VIBER])) {
                                $names = ArrayHelper::merge($names, [ArrayHelper::getValue($nameData, "name")]);
                            } else {
                                $names = ArrayHelper::merge($names, ArrayHelper::getColumn($nameData, "name"));
                            }
                        }
                    }

                    $names = array_unique($names);
                    $names = array_splice($names, 0, 2);

                    return join(", ", $names);
                }
            ],
            [
                'header' => 'Стоимость',
                'value' => function(SearchRequest $model) {
                    $type = "";
                    switch ($model->is_payed) {
                        case 0: $type = "Бесплатный (нет проверок)"; break;
                        case 1: $type = "Платный"; break;
                        case 2: $type = "Бесплатный (не нашли)"; break;

                    }
                    return $type;
                }
            ],
            [
                'header' => 'Индекс поиска',
                'value' => function(SearchRequest $model) {
                    return array_sum(array_map(function(RequestResult $result) {
                        return $result->index;
                    }, $model->results));
                }
            ],
        ]
    ]) ?>
</div>
