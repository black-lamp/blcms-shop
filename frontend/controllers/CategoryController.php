<?php
namespace bl\cms\multishop\frontend\controllers;

use bl\cms\multishop\common\entities\Category;
use bl\cms\multishop\common\entities\Product;
use yii\db\Query;
use yii\web\Controller;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class CategoryController extends Controller
{
    public function actionShow($categoryId = null) {
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