<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * This widget displays the recommended products
 */

namespace bl\cms\shop\widgets;

use bl\cms\shop\common\entities\Product;
use yii\base\Widget;
use yii\helpers\ArrayHelper;

class RecommendedProducts extends Widget
{
    public $id;

    public function init()
    {
        parent::init();

    }

    public function run()
    {
        parent::run();

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

            $previous = Product::find()->where(['<', 'id', $id])
                ->andWhere(['category_id' => $categoryId])->orderBy(['id' => SORT_DESC])->limit('2')->all();
            $next = Product::find()->where(['>', 'id', $id])
                ->andWhere(['category_id' => $categoryId])->orderBy(['id' => SORT_ASC])->limit('2')->all();

            $products = ArrayHelper::merge($previous, $next);

            return $products;
        }

        return false;
    }
}