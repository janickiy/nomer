<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "vk_comments".
 *
 * @property integer $id
 * @property integer $pid
 * @property integer $site_id
 * @property string $tm
 * @property string $name
 * @property string $comment
 * @property string $vk_id
 * @property string $photo
 */
class VkComment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vk_comments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pid', 'site_id'], 'integer'],
            [['tm'], 'safe'],
            [['comment', 'photo'], 'string'],
            [['name', 'vk_id'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pid' => 'Pid',
            'site_id' => 'Site ID',
            'tm' => 'Tm',
            'name' => 'Name',
            'comment' => 'Comment',
            'vk_id' => 'Vk ID',
            'photo' => 'Photo',
        ];
    }

    public function getComments() {
        return $this->hasMany(VkComment::className(), ["pid" => "id"])->andWhere(["site_id" => $this->site_id]);
    }
}
