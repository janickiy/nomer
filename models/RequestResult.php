<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "request_results".
 *
 * @property integer $id
 * @property integer $request_id
 * @property integer $type_id
 * @property integer $index
 * @property string $data
 * @property integer $cache_id
 * @property SearchRequest $request
 */
class RequestResult extends \yii\db\ActiveRecord
{

        /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'request_results';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['request_id', 'type_id', 'index', 'cache_id'], 'integer'],
            [['data'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'request_id' => 'Request ID',
            'type_id' => 'Type ID',
            'index' => 'Index',
            'data' => 'Data',
            'cache_id' => 'Cache ID',
        ];
    }

    public function getRequest()
    {
        return $this->hasOne(SearchRequest::className(), ['id' => 'request_id']);
    }
}
