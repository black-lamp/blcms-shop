<?php
namespace bl\cms\shop\frontend\controllers;

use bl\cms\shop\common\entities\Currency;
use bl\cms\shop\common\entities\ProductAvailability;
use bl\cms\shop\common\entities\Vendor;
use bl\cms\shop\frontend\models\FilterModel;
use bl\cms\shop\widgets\traits\TreeWidgetTrait;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use bl\cms\cart\models\CartForm;
use bl\cms\seo\StaticPageBehavior;
use bl\cms\shop\frontend\components\ProductSearch;
use bl\cms\shop\common\entities\{
    Category, Filter
};
use yii\web\NotFoundHttpException;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class CategoryController extends Controller
{
    use TreeWidgetTrait;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'staticPage' => [
                'class' => StaticPageBehavior::className(),
                'key' => 'shop'
            ]
        ];
    }

    /**
     * @param null $id
     * @return string
     * @throws NotFoundHttpException
     * Shows parent categories and products of this category if category has not children categories.
     */
    public function actionShow($id = null)
    {
        /* @var Category $category */
        $requestParams = Yii::$app->request->queryParams;
        $descendantCategories = null;
        if (is_null($id)) {
            $childCategories = Category::find()
                ->where(['parent_id' => null, 'show' => true, 'additional_products' => false])
                ->orderBy(['position' => SORT_ASC])->all();
            $descendantCategories = [];
            foreach ($childCategories as $childCategory) {
                $descendantCategories = array_merge($descendantCategories, $childCategory->getDescendants());
            }
            $this->registerStaticSeoData();
        } else {
            $category = Category::find()->where(['id' => $id, 'additional_products' => false])->one();
            if (empty($category)) throw new NotFoundHttpException();

            $category->registerMetaData();

            $childCategories = $category->getChildren();
            $descendantCategories = $category->getDescendants();
            array_push($descendantCategories, $category);

            // filtering
        }

        $shopFilterModel = new FilterModel();
        $searchModel = new ProductSearch($requestParams, $descendantCategories, $shopFilterModel);
        $filterParams = [];

        if(!empty($id)) {
            if($shopFilterModel->loadFromCategory($category, Yii::$app->request->get(), '')) {
                $shopFilterModel->maxPrice = $this->convertPrice($searchModel->getMaxProductPrice());
                $shopFilterModel->minPrice = $this->convertPrice($searchModel->getMinProductPrice());

                if(empty($shopFilterModel->pto)) {
                    $shopFilterModel->pto = $shopFilterModel->maxPrice;
                }
                if(empty($shopFilterModel->pfrom)) {
                    $shopFilterModel->pfrom = $shopFilterModel->minPrice;
                }
            }

            $productFilter = $category->filter;
            $filterParams = $productFilter->params;
            if(!empty($filterParams)) {
                foreach ($filterParams as $filterParam) {
                    if($filterParam->all_values) {
                        $filterParam->params = $searchModel->getFilterParamValues($filterParam->translation->param_name);
                    }
                }
            }
        }
        else {
            $shopFilterModel = null;
        }

        if ($this->module->showChildCategoriesProducts || empty($childCategories)) {
            $cart = new CartForm();
            $dataProvider = $searchModel->search();
        }
        if(Yii::$app->request->get('page') > 1) {
            $this->view->registerMetaTag([
                'name' => 'robots',
                'content' => 'noindex, follow'
            ]);
        }

        $this->view->registerLinkTag([
            'rel' => 'canonical',
            'href' => Url::to([
                '/shop/category/show',
                'id' => $id
            ])
        ]);

        return $this->render(empty($category->view) ? 'show' : $category->view, [
            'category' => $category ?? null,
            'childCategories' => $childCategories,
            'filters' => $filters ?? null,
            'cart' => $cart ?? null,
            'dataProvider' => $dataProvider ?? null,
            'shopFilterModel' => $shopFilterModel,
            'vendors' => $searchModel->getVendors(),
            'availabilities' => ProductAvailability::find()->all(),
            'categoryFilterParams' => $filterParams
        ]);

    }

    private function convertPrice($price) {
        if($this->module->enableCurrencyConversion) {
            $price = floor($price * Currency::currentCurrency() * 100) / 100;
        }

        return $price;
    }
}