<?php
namespace bl\cms\shop\frontend\controllers;
use bl\cms\shop\common\entities\Category;
use bl\cms\shop\common\entities\Product;
use yii\db\Query;
use yii\web\Controller;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class CategoryController extends Controller
{
    public function actionShow($categoryId = null) {
        /*Getting SEO data*/
        $category = Category::findOne($categoryId);
        $this->view->title = $category->translation->seoTitle;
        $this->view->registerMetaTag([
            'name' => 'description',
            'content' => html_entity_decode($category->translation->seoDescription)
        ]);
        $this->view->registerMetaTag([
            'name' => 'keywords',
            'content' => html_entity_decode($category->translation->seoKeywords)
        ]);

        /*Getting products of category*/
        $query = Product::find();
        if($categoryId != null) {
            $query->where([
                'category_id' => $categoryId
            ]);
        }

        return $this->render('show', [
            'menuItems' => Category::find()->with(['translations'])->all(),
            'category' => Category::findOne($categoryId),
            'products' => $query->all()
        ]);
        
    }
}