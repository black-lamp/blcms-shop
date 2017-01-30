<?php

namespace bl\cms\shop\frontend\components;

use bl\cms\shop\common\entities\FilterType;
use yii\base\Exception;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use bl\cms\shop\common\entities\Product;
use yii\helpers\ArrayHelper;

/**
 * ProductSearch represents the model behind the search form about `bl\cms\shop\common\entities\Product`.
 */
class ProductSearch extends Product
{

    /**
     * Sorting methods.
     */
    CONST SORT_CHEAP = 'cheap';
    CONST SORT_EXPENSIVE = 'expensive';
    CONST SORT_NEW = 'new';
    CONST SORT_OLD = 'old';


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
    public function search($params, $descendantCategories)
    {

        $this->load($params, '');
        $query = Product::find();

        if (\Yii::$app->controller->module->showChildCategoriesProducts) {

            if (!empty($params['id'])) {
                $query->where(['in', 'category_id', ArrayHelper::map($descendantCategories, 'id', 'id')]);
            }

        }
        else {
            if (!empty($params['id'])) {
                $query->where(['category_id' => $params['id']]);
            }
        }

        $query->andWhere(['status' => Product::STATUS_SUCCESS]);


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

        if (isset($params['sort'])) {
            switch ($params['sort']) {
                case self::SORT_CHEAP:
                    $query->joinWith('combinations.combinationPrices.price p');
                    $query->joinWith('productPrices.price u');

                    $query->orderBy(['u.price' => SORT_ASC, 'p.price' => SORT_ASC]);
                    break;

                case self::SORT_EXPENSIVE:
                    $query->joinWith('combinations.combinationPrices.price p');
                    $query->joinWith('productPrices.price u');

                    $query->orderBy(['u.price' => SORT_DESC, 'p.price' => SORT_DESC]);
                    break;

                case self::SORT_OLD:
                    $query->orderBy(['creation_time' => SORT_ASC]);
                    break;

                case self::SORT_NEW:
                    $query->orderBy(['creation_time' => SORT_DESC]);
                    break;
            }
        } else {
            $query->orderBy(['category_id' => SORT_ASC, 'position' => SORT_ASC]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        if ($this->validate()) {
            return $dataProvider;
        }
        else throw new Exception();
    }
}