<?php

namespace bl\cms\shop\frontend\components;

use bl\cms\shop\common\entities\Currency;
use bl\cms\shop\common\entities\FilterParamValue;
use bl\cms\shop\common\entities\Param;
use bl\cms\shop\common\entities\ParamTranslation;
use bl\cms\shop\common\entities\Vendor;
use bl\cms\shop\frontend\models\FilterModel;
use bl\multilang\entities\Language;
use yii\base\Exception;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use bl\cms\shop\common\entities\Product;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * ProductSearch represents the model behind the search form about `bl\cms\shop\common\entities\Product`.
 */
class ProductSearch extends Product
{

    private $discountPriceQuery = 'IF(COALESCE(p.discount_type_id, u.discount_type_id) IS NULL, COALESCE(p.price, u.price), IF( COALESCE(p.discount_type_id, u.discount_type_id) = 2, COALESCE(p.price, u.price) - (COALESCE(p.price, u.price) / 100 * COALESCE(p.discount, u.discount)) , COALESCE(p.price, u.price) - COALESCE(p.discount, u.discount)))';

    private $params;
    private $categoryId;
    private $descendantCategories;
    /**
     * @var FilterModel
     */
    private $filterModel = null;
    /**
     * Sorting methods.
     */
    CONST SORT_CHEAP = 'cheap';
    CONST SORT_EXPENSIVE = 'expensive';
    CONST SORT_NEW = 'new';
    CONST SORT_OLD = 'old';

    /**
     * ProductSearch constructor.
     * @param $params
     * @param $descendantCategories
     * @param null $filterModel
     */
    public function __construct($params, $descendantCategories, $filterModel = null)
    {
        parent::__construct();
        $this->params = $params;
        $this->descendantCategories = $descendantCategories;
        $this->filterModel = $filterModel;
        if(!empty($this->params['id'])) {
            $this->categoryId = $this->params['id'];
        }
    }

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
     * @return ActiveDataProvider
     * @throws Exception if search is not validated
     */
    public function search()
    {
        $this->load($this->params, '');
        $query = $this->getBasicQuery();
        $languageId = Language::getCurrent()->id;

        if(!empty($this->filterModel) && $this->filterModel instanceof FilterModel) {
            if(!empty($this->filterModel->pfrom) && $this->filterModel->pfrom != $this->filterModel->minPrice) {
                $query->andWhere(['>=', $this->discountPriceQuery, $this->convertPrice($this->filterModel->pfrom)]);
            }
            if(!empty($this->filterModel->pto) && $this->filterModel->pto != $this->filterModel->maxPrice) {
                $query->andWhere(['<=', $this->discountPriceQuery, $this->convertPrice($this->filterModel->pto)]);
            }
            if(!empty($this->filterModel->vendors)) {
                $query->andWhere(['in', 'shop_product.vendor_id', $this->filterModel->vendors]);
            }
            if(!empty($this->filterModel->availabilities)) {
                $query->andWhere(['in', 'shop_product.availability', $this->filterModel->availabilities]);
            }

            // TODO: rework query
            if(!empty($this->filterModel->params)) {
                $query->leftJoin('shop_param', 'shop_product.id = shop_param.product_id');
                foreach ($this->filterModel->params as $key => $param) {
                    $query->leftJoin('shop_param_translation param_' . $key, "`shop_param`.`id` = `param_$key`.`param_id` AND `param_$key`.`language_id` = $languageId AND `param1`.`name` = ''");
                }
            }
        }


        switch (ArrayHelper::getValue($this->params, 'sort')) {
            case self::SORT_CHEAP:
                $query->select([
                    '`shop_product`.*',
                    $this->discountPriceQuery . ' AS discount_price'
                ]);

                $query->orderBy(['discount_price' => SORT_ASC]);
                break;

            case self::SORT_EXPENSIVE:
                $query->select([
                    '`shop_product`.*',
                    $this->discountPriceQuery . ' AS discount_price'
                ]);

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

    public function getMaxProductPrice() {
        $query = $this->getBasicQuery();
        $result = $query->max($this->discountPriceQuery);
        return $result;
    }

    public function getMinProductPrice() {
        $query = $this->getBasicQuery();
        $result = $query->min($this->discountPriceQuery);
        return $result;
    }

    public function getVendors() {
        return Vendor::find()
            ->joinWith('products p')
            ->where(['in', 'p.category_id', ArrayHelper::map($this->descendantCategories, 'id', 'id')])
            ->groupBy(['id'])
            ->all();
    }

    /**
     * @param $param_name
     * @return Param[]
     */
    public function getFilterParamValues($param_name)
    {
        return Param::find()
            ->select('t.value')
            ->with('translation')
            ->joinWith('translation t')
            ->joinWith('product p')
            ->where(['in', 'p.category_id', ArrayHelper::map($this->descendantCategories, 'id', 'id')])
            ->andWhere(['t.name' => $param_name])
            ->groupBy(['t.value'])
            ->asArray()
            ->all();
    }

    /**
     * @return ActiveQuery $this
     */
    private function getBasicQuery() {
        $query = Product::find()->joinWith('category')
            ->joinWith('defaultCombination.currentCombinationPrice.price p')
            ->joinWith('currentProductPrice.price u');

        if (\Yii::$app->controller->module->showChildCategoriesProducts) {

            if (!empty($this->params['id'])) {
                $query->where(['in', 'category_id', ArrayHelper::map($this->descendantCategories, 'id', 'id')]);
            }

        }
        else {
            if (!empty($this->params['id'])) {
                $query->where(['category_id' => $this->params['id']]);
            }
        }

        $query->andWhere(['status' => Product::STATUS_SUCCESS, 'shop_product.show' => true, 'additional_products' => false]);

        return $query;
    }

    private function convertPrice($price) {
        if(\Yii::$app->getModule('shop')->enableCurrencyConversion) {
            $price = $price / Currency::currentCurrency();
        }

        return $price;
    }
}