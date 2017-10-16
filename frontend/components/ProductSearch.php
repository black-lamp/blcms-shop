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
        $query = Product::find()->joinWith('category');

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

        $query->andWhere(['status' => Product::STATUS_SUCCESS, 'shop_product.show' => true, 'additional_products' => false]);

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

        switch (ArrayHelper::getValue($params, 'sort')) {
            case self::SORT_CHEAP:
                $query->select([
                    '`shop_product`.*',
                    'IF(COALESCE(p.discount_type_id, u.discount_type_id) IS NULL, COALESCE(p.price, u.price), IF( COALESCE(p.discount_type_id, u.discount_type_id) = 2, COALESCE(p.price, u.price) - (COALESCE(p.price, u.price) / 100 * COALESCE(p.discount, u.discount)) , COALESCE(p.price, u.price) - COALESCE(p.discount, u.discount))) AS discount_price'
                ]);
                $query->joinWith('defaultCombination.combinationPrices.price p');
                $query->joinWith('productPrices.price u');

                $query->orderBy(['discount_price' => SORT_ASC]);
                break;

            case self::SORT_EXPENSIVE:
                $query->select(['`shop_product`.*', '(COALESCE(p.price, u.price) - COALESCE(p.discount, u.discount)) as discount_price']);
                $query->joinWith('defaultCombination.combinationPrices.price p');
                $query->joinWith('productPrices.price u');

                $query->orderBy(['discount_price' => SORT_DESC]);
                break;

            case self::SORT_OLD:
                $query->orderBy(['creation_time' => SORT_ASC]);
                break;

            case self::SORT_NEW:
                $query->orderBy(['creation_time' => SORT_DESC]);
                break;

            default:
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