<?php

use yii\db\Migration;

use \app\models\SearchRequest;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class m170605_071802_add_request_fields extends Migration
{
    public function up()
    {
        $this->addColumn(SearchRequest::tableName(), "is_has_name", $this->boolean()->defaultValue(false));
        $this->addColumn(SearchRequest::tableName(), "is_has_photo", $this->boolean()->defaultValue(false));

        $i = 0;
        foreach(SearchRequest::find()->with("results")->orderBy(["id" => SORT_ASC])->batch(100) as $requests) {
            $i += 100;
            foreach($requests as $r) {
                /* @var app\models\SearchRequest $r */
                $names = $photos = [];
                $results = \app\models\RequestResult::find()->where(["request_id" => $r->id])->all();
                foreach($results as $result) {
                    try {
                        $data = Json::decode($result->data);
                        if($data && is_array($data)) {
                            $names = ArrayHelper::merge($names, ArrayHelper::getColumn($data, "name"));
                            $photos = ArrayHelper::merge($photos, ArrayHelper::getColumn($data, "photo"));
                        }
                    } catch(Exception $e) {
                        continue;
                    }
                }
                $names = array_filter($names);
                $photos = array_filter($photos);
                if($names) {
                    $r->is_has_name = true;
                }
                if($photos) {
                    $r->is_has_photo = true;
                }
                $r->save();
            }
            \yii\helpers\Console::output("proccessed: ".$i);
        }
    }

    public function down()
    {
        $this->dropColumn(SearchRequest::tableName(), "is_has_name");
        $this->dropColumn(SearchRequest::tableName(), "is_has_photo");
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
