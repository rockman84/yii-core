<?php

namespace sky\yii\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use sky\yii\models\Currency;

/**
 * CurrencySearch represents the model behind the search form of `sky\yii\models\Currency`.
 */
class CurrencySearch extends Currency
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'rate_updated_at', 'status', 'weight', 'created_at', 'updated_at'], 'integer'],
            [['name', 'code', 'symbol', 'decimal_point', 'thousand_separator', 'prefix', 'suffix'], 'safe'],
            [['rate'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = Currency::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'rate' => $this->rate,
            'rate_updated_at' => $this->rate_updated_at,
            'status' => $this->status,
            'weight' => $this->weight,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'symbol', $this->symbol])
            ->andFilterWhere(['like', 'decimal_point', $this->decimal_point])
            ->andFilterWhere(['like', 'thousand_separator', $this->thousand_separator])
            ->andFilterWhere(['like', 'prefix', $this->prefix])
            ->andFilterWhere(['like', 'suffix', $this->suffix]);

        return $dataProvider;
    }
}
