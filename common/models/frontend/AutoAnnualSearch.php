<?php

namespace common\models\frontend;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AutoMileageSearch represents the model behind the search form about `common\models\frontend\AutoMileage`.
 */
class AutoAnnualSearch extends AutoAnnual
{
    /**
     * Get the validation rules that apply to the model.
     *
     * @return array
     */
    public function rules()
    {
        return [
            [
                [
                    'id',
                    'auto_id',
                    'year',
                    'date_entered',
                    'note',
                    'locked',
                    'dateFrom',
                    'dateTo',
                ],
                'safe'
            ],
            [
                [
                    'year'
                ],
                'number'
            ],
        ];
    }


    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = AutoAnnual::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'key' => function ($model) {
                return ($model->auto_id.'_'.$model->year);
            }

        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }


        if (!is_null($this->year)) {
            $query->andFilterWhere([
                '=',
                'year',
                $this->year
            ]);
        }

        if (!is_null($this->auto_id)) {
            $query->andFilterWhere([
                '=',
                'auto_id',
                $this->auto_id
            ]);
        }
        $query->orderBy(['year'=>SORT_DESC]);
        return $dataProvider;
    }
}
