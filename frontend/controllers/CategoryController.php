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
        $category = null;
        $productsQuery = Product::find();

        if(!empty($categoryId)) {
            $category = Category::findOne($categoryId);
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
                'category_id' => $categoryId
            ]);
        }

        return $this->render('show', [
            'menuItems' => Category::find()->with(['translations'])->all(),
            'category' => $category,
            'products' => $productsQuery->all()
        ]);
        
    }
}