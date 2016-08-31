<?php
namespace bl\cms\shop\frontend\controllers;
use bl\cms\seo\StaticPageBehavior;
use bl\cms\shop\common\entities\Category;
use bl\cms\shop\common\entities\Product;
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

        return $this->render('show', [
            'menuItems' => Category::find()->orderBy(['position' => SORT_ASC])->with(['translations'])->all(),
            'category' => $category,
            'products' => $productsQuery->orderBy(['category_id' => SORT_ASC, 'position' => SORT_ASC])->all()
        ]);
        
    }
}