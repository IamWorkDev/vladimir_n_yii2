<?php

namespace common\models\frontend;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AutoMileageSearch represents the model behind the search form about `common\models\frontend\AutoMileage`.
 */
class AutoMileageSearch extends AutoMileage
{
    public $dateFrom;
    public $dateTo;
    public $totalMileage = 0;

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
                    'dateFrom',
                ],
                'default',
                'value' => (new \DateTime())->sub(new \DateInterval('P1M'))->format('Y-m-d')
            ],
            [
                [
                    'id',
                    'auto_id',
                    'date',
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
                    'odo_start',
                    'odo_end',
                    'mileage'
                ],
                'number'
            ],
        ];
    }

    /**
     * Set the values for dateFrom and dateTo attributes
     *
     * @return bool
     */
    public function beforeValidate()
    {
        $this->dateFrom = $this->toStorageDate($this->dateFrom);
        $this->dateTo = $this->toStorageDate($this->dateTo);

        return parent::beforeValidate();
    }

    /**
     * @inheritdoc
     */
    public function afterValidate()
    {
        parent::afterValidate();
    }


    /**
     * @inheritdoc
     */
    public function scenarios()
    {
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
        $query = AutoMileage::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if (!is_null($this->dateFrom)) {
            $query->andFilterWhere([
                '>=',
                'date',
                $this->dateFrom
            ]);
        }

        if (!is_null($this->dateTo)) {
            $query->andFilterWhere([
                '<=',
                'date',
                $this->dateTo
            ]);
        }

        $query->andFilterWhere([
            'like',
            'id',
            $this->id
        ])
            ->andFilterWhere([
                'like',
                'auto_id',
                $this->auto_id
            ])
            ->andFilterWhere([
                'like',
                'note',
                $this->note
            ]);
        $this->dateFrom = $this->toAppDate($this->dateFrom);
        $this->dateTo = $this->toAppDate($this->dateTo);
        $this->totalMileage = $query->sum('mileage');

        return $dataProvider;
    }
}
