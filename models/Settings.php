<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "settings".
 *
 * @property integer $id
 * @property string $param
 * @property string $value
 */
class Settings extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'settings';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['param', 'value'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'param' => 'Param',
            'value' => 'Value',
        ];
    }

    public static function get($param, $defaultValue = null) {
        $p = self::find()->where(["param" => $param])->one();
        if(is_null($p)) return $defaultValue;
        return ArrayHelper::getValue($p, "value");
    }

    public static function set($param, $value) {
        $p = self::find()->where(["param" => $param])->one();
        if(is_null($p)) {
            $p = new self;
            $p->param = $param;
        }
        $p->value = $value;
        $p->save();
    }
}
