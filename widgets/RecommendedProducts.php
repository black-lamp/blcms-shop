<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * This widget displays the recommended products
 */

namespace bl\cms\shop\widgets;


use bl\cms\shop\common\entities\Category;
use bl\cms\shop\common\entities\Product;
use yii\base\Widget;

class RecommendedProducts extends Widget
{
    public $id;

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub

    }

    public function run()
    {
        parent::run(); // TODO: Change the autogenerated stub

        $products = $this->findRecommendedProducts($this->id);
        if (!empty($products)) {
            return $this->render('recommended-products',
                [
                    'recommendedProducts' => $products
                ]);
        }
        else return false;
    }

    private function findRecommendedProducts($id) {

        if (!empty($id)) {
            $product = Product::findOne($id);
            $categoryId = $product->category_id;

            $previous = Product::find()->where(['<', 'id', $id])->andWhere(['category_id' => $categoryId])->orderBy(['id' => SORT_DESC])->limit('2')->all();
            $next = Product::find()->where(['>', 'id', $id])->andWhere(['category_id' => $categoryId])->orderBy(['id' => SORT_ASC])->limit('2')->all();
            if (empty($next[1]) && !empty($next[0])) {
                $next[1] = Product::find()->where(['<', 'id', $id])->andwhere(['category_id' => $categoryId])->orderBy(['id' => SORT_ASC])->one();
            }
            if (empty($next[0])) {
                $next = Product::find()->where(['<', 'id', $id])->andwhere(['category_id' => $categoryId])->orderBy(['id' => SORT_ASC])->limit('2')->all();
            }
            if (empty($previous[1]) && !empty($previous[0])) {
                $previous[1] = Product::find()->where(['>', 'id', $id])->andwhere(['category_id' => $categoryId])->orderBy(['id' => SORT_DESC])->one();
            }
            if (empty($previous[0])) {
                $previous = Product::find()->where(['>', 'id', $id])->andwhere(['category_id' => $categoryId])->orderBy(['id' => SORT_DESC])->limit('2')->all();
            }
            $recommendedProducts = [];
            if (!empty($previous[1])) {
                $recommendedProducts[] = $previous[1];
            }
            if (!empty($previous[0])) {
                $recommendedProducts[] = $previous[0];
            }
            if (!empty($next[0])) {
                $recommendedProducts[] = $next[0];
            }
            if (!empty($next[1])) {
                $recommendedProducts[] = $next[1];
            }
            return $recommendedProducts;
        }

        return false;
    }
}