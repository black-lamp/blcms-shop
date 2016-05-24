<?php
namespace bl\cms\shop\frontend\controllers;

use bl\cms\shop\common\entities\Category;
use bl\cms\shop\common\entities\Product;
use yii\web\Controller;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class ProductController extends Controller
{
    public function actionIndex() {
        return $this->render('index', [
            'categories' => Category::find()->with(['translations'])->all(),
            'products' => Product::find()->with(['translations'])->all(),
        ]);
    }

    public function actionShow($id = null) {
        $product = Product::findOne($id);
        return $this->render('show', [
            'categories' => Category::find()->with(['translations'])->all(),
            'product' => $product,
        ]);
    }
}