<?php
namespace bl\cms\shop\frontend\controllers;

use bl\cms\shop\common\entities\Category;
use bl\cms\shop\common\entities\Param;
use bl\cms\shop\common\entities\ParamTranslation;
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\ProductPrice;
use Yii;
use yii\web\Controller;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class ProductController extends Controller
{
    public function actionIndex() {
        $products = Product::find()->with(['translations'])->all();
        $categories = Category::find()->with(['translations'])->all();
        return $this->render('index', [
            'categories' => $categories,
            'products' => $products,
        ]);
    }

    public function actionShow($id = null) {
        $product = Product::findOne($id);

        /*Getting SEO data*/
        $this->view->title = $product->translation->seoTitle;
        $this->view->registerMetaTag([
            'name' => 'description',
            'content' => html_entity_decode($product->translation->seoDescription)
        ]);
        $this->view->registerMetaTag([
            'name' => 'keywords',
            'content' => html_entity_decode($product->translation->seoKeywords)
        ]);

        return $this->render('show', [
            'categories' => Category::find()->with(['translations'])->all(),
            'product' => $product,
            'params' => Param::find()->where([
                'product_id' => $id
                ])->all(),
        ]);
    }
}