<?php
namespace bl\cms\shop\frontend\controllers;
use bl\cms\cart\models\CartForm;
use bl\cms\seo\StaticPageBehavior;
use bl\cms\shop\common\entities\Category;
use bl\cms\shop\common\entities\Filter;
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\frontend\components\ProductSearch;
use Yii;
use yii\base\Exception;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class CategoryController extends Controller
{
    public function behaviors()
    {
        return [
            'staticPage' => [
                'class' => StaticPageBehavior::className(),
                'key' => 'shop'
            ]
        ];
    }

    public function actionShow($id = null) {

        $category = null;
        $productsQuery = Product::find();

        if(!empty($id)) {
            $category = Category::findOne($id);
            if(!empty($category->translation->seoTitle)) {
                $this->view->title = $category->translation->seoTitle;
            }
            else {
                $this->view->title = $category->translation->title;
            }
            $this->view->registerMetaTag([
                'name' => 'description',
                'content' => html_entity_decode($category->translation->seoDescription)
            ]);
            $this->view->registerMetaTag([
                'name' => 'keywords',
                'content' => html_entity_decode($category->translation->seoKeywords)
            ]);

            $productsQuery->where([
                'category_id' => $id
            ]);
        }
        else {
            $this->registerStaticSeoData();
        }

        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $filters = (!empty($category->id)) ? Filter::find()->where(['category_id' => $category->id])->all() :
            null;
        $cart = new CartForm();

        return $this->render('show', [
            'category' => $category,
            'menuItems' => Category::find()->orderBy(['position' => SORT_ASC])->with(['translations'])->all(),
            'filters' => $filters,
            'products' => Product::find()->all(),
            'cart' => $cart,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
        
    }

    public function actionGetCategories($parentId = null, $level, $currentCategoryId) {
        if (\Yii::$app->request->isAjax) {

            if (!empty($level)) {
                $categories = Category::find()->where(['parent_id' => $parentId])->all();

                return $this->renderAjax('@vendor/black-lamp/blcms-shop/widgets/views/tree/categories-ajax', [
                    'categories' => $categories,
                    'level' => $level,
                    'currentCategoryId' => $currentCategoryId,
                ]);
            }

            else throw new Exception();
        }
        else throw new BadRequestHttpException();
    }
}