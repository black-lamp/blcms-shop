<?php
namespace bl\cms\shop\frontend\controllers;

use Yii;
use yii\base\Exception;
use bl\cms\cart\models\CartForm;
use bl\cms\seo\StaticPageBehavior;
use yii\web\{
    BadRequestHttpException, Controller
};
use bl\cms\shop\frontend\components\ProductSearch;
use bl\cms\shop\common\entities\{
    Category, Filter
};

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class CategoryController extends Controller
{
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
     * @param null|integer $id
     * @return string
     *
     * Shows parent categories and products of this category if category has not children categories.
     */
    public function actionShow($id = null)
    {

        if (is_null($id)) {
            $descendantCategories = Category::find()->all();
            $this->registerStaticSeoData();
        } else {
            $category = Category::findOne($id);
            $category->registerMetaData();

            $descendantCategories = $category->getDescendants($category);

            if (empty($descendantCategories)) {

                $filters = Filter::find()->where(['category_id' => $category->id])->all();
                $cart = new CartForm();
            }
        }

        $searchModel = new ProductSearch();
        $descendantCategoriesWithParent = $descendantCategories;
        if (!empty($category)) {
            array_push($descendantCategoriesWithParent, $category);
        }
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $descendantCategoriesWithParent);

        return $this->render('show', [
            'category' => $category ?? null,
            'descendantCategories' => $descendantCategories,
            'filters' => $filters ?? null,
            'cart' => $cart ?? null,
            'dataProvider' => $dataProvider ?? null,
        ]);

    }

    /**
     * @param null|integer $parentId
     * @param integer $level
     * @param integer $currentCategoryId
     * @return mixed
     * @throws BadRequestHttpException
     * @throws Exception
     *
     * This action is used by Tree wiget
     */
    public function actionGetCategories($parentId = null, $level, $currentCategoryId)
    {
        if (\Yii::$app->request->isAjax) {

            if (!empty($level)) {
                $categories = Category::find()->where(['parent_id' => $parentId])->orderBy('position')->all();

                return $this->renderAjax('@vendor/black-lamp/blcms-shop/widgets/views/tree/categories-ajax', [
                    'categories' => $categories,
                    'level' => $level,
                    'currentCategoryId' => $currentCategoryId,
                ]);
            } else throw new Exception();
        } else throw new BadRequestHttpException();
    }
}