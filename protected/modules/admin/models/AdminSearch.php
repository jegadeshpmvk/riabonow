<?php

namespace app\modules\admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Admin;

class AdminSearch extends Admin
{

    public function rules()
    {
        return [
            [['username', 'email'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = Admin::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['defaultPageSize' => Yii::$app->user->identity->getCookie('list_total')]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andWhere(['>=', 'deleted', 0]);

        $query->andFilterWhere([
            'deleted' => trim($this->deleted) != "" ? (int) $this->deleted : $this->deleted
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'email', $this->email]);

        return $dataProvider;
    }
}
