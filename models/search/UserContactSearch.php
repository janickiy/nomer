<?php

namespace app\models\search;

use Yii;
use yii\data\ActiveDataProvider;
use app\models\UserContact;

class UserContactSearch extends UserContact
{
    /**
     * @var string
     */
    public $phone;

    /**
     * @var string
     */
    public $name;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['phone', 'name'], 'string']
        ];
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search(array $params)
    {
        $query = UserContact::find()->where(['user_id' => Yii::$app->getUser()->getIdentity()->id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['name' => SORT_ASC]]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        if ($this->phone) {
            $query->andFilterWhere(['like', 'phone', $this->phone]);
        }

        if ($this->name) {
            $query->andFilterWhere(['like', 'name', $this->name]);
        }

        return $dataProvider;
    }
}
