<?php

namespace bl\cms\shop\frontend\components;

use bl\cms\shop\common\entities\FilterType;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use bl\cms\shop\common\entities\Product;

/**
 * ProductSearch represents the model behind the search form about `bl\cms\shop\common\entities\Product`.
 */
class ProductSearch extends Product
{

//    public $vendor_id;
//    public $country_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['country_id', 'vendor_id'], 'safe'],
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
     * @param array $params
     * @return ActiveDataProvider
     * @throws Exception if search is not validated
     */
    public function search($params)
    {
        $this->load($params, '');
        $query = Product::find();

        if (!empty($params['id'])) {
            $query->where(['category_id' => $params['id']]);
        }

        $filterTypes = FilterType::find()->all();
        foreach ($filterTypes as $filterType) {
            $className = $filterType->class_name;

            /*Getting get-method name*/
            $getMethodName = explode("\\", $className);
            $getMethodName = lcfirst(end($getMethodName));

            $query->joinWith($getMethodName);


            $tableName = $className::tableName();
            $column = $filterType->column;


            $query->andFilterWhere([$tableName . '.' . 'id' => $this->$column]);
        }

        $query->orderBy(['category_id' => SORT_ASC, 'position' => SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ]
        ]);

        if ($this->validate()) {
            return $dataProvider;
        }
        else throw new Exception();
    }
}