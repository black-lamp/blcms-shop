<?php

namespace bl\cms\shop\frontend\components;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use bl\cms\shop\common\entities\Product;

/**
 * ProductSearch represents the model behind the search form about `bl\cms\shop\common\entities\Product`.
 */
class ProductSearch extends Product
{

    public $vendor_id;
    public $country_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['country_id', 'vendor_id'], 'safe']
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
        $this->load($params, '');
        $query = Product::find()
            ->with('vendor')
            ->where(['category_id' => $params['id']])
            ->andFilterWhere(['vendor_id' => $this->vendor_id])
            ->andFilterWhere(['country_id' => $this->country_id])
            ->orderBy(['category_id' => SORT_ASC, 'position' => SORT_ASC]);

        $query->andFilterWhere(['shop_product.status' => $this->status]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!$this->validate()) {

            return $dataProvider;
        }



        return $dataProvider;
    }
}