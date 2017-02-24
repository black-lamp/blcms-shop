<?php
namespace bl\cms\shop\frontend\widgets;

use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\RelatedProduct;
use yii\base\Widget;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * Example:
 * <?= \bl\cms\shop\frontend\widgets\RelatedProducts::widget(['productId' => [$product->id]]); ?>
 */
class RelatedProducts extends Widget
{

    /**
     * @var integer|array
     */
    public $productId;

    public function init()
    {

    }

    public function run()
    {
        parent::run();

        $relatedProducts = (is_array($this->productId)) ?
            Product::find()->joinWith('relatedProductsWhereItRelated')
                ->where(['in', 'shop_related_product.product_id', $this->productId])->all() :
            RelatedProduct::find()->where(['product_id' => $this->productId])->all();


        return $this->render('_products',
            [
                'products' => $relatedProducts
            ]);

    }
}