<?php
namespace bl\cms\shop\frontend\controllers;

use bl\cms\shop\widgets\traits\TreeWidgetTrait;
use Yii;
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

        if (is_null($id)) {
            $childCategories = Category::find()->where(['parent_id' => null, 'show' => true, 'additional_products' => NULL])->all();
            $this->registerStaticSeoData();
        } else {
            $category = Category::find()->where(['id' => $id, 'additional_products' => NULL])->one();
            if (empty($category)) throw new NotFoundHttpException();

            $category->registerMetaData();

            $childCategories = $category->getChildren();
            $descendantCategories = $category->getDescendants($category);
            array_push($descendantCategories, $category);


        }
        if ($this->module->showChildCategoriesProducts || empty($childCategories)) {

            $filters = (!empty($category)) ?
                Filter::find()->where(['category_id' => $category->id])->all() : null;

            $cart = new CartForm();
            $searchModel = new ProductSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $descendantCategories ?? null);
        }

        return $this->render('show', [
            'category' => $category ?? null,
            'childCategories' => $childCategories,
            'filters' => $filters ?? null,
            'cart' => $cart ?? null,
            'dataProvider' => $dataProvider ?? null,
        ]);

    }
}