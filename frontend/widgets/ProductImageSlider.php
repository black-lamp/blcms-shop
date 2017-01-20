<?php
namespace bl\cms\shop\frontend\widgets;


use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\ProductImage;
use yii\helpers\Html;
use evgeniyrru\yii2slick\Slick;

/**
 * Widget renders product images.
 * @see https://github.com/EvgeniyRRU/yii2-slick
 * @see http://kenwheeler.github.io/slick/
 *
 * Example:
 * ```php
 * <?= \bl\cms\shop\frontend\widgets\ProductImageSlider::widget([
 *      'product' => $product,
 *
 *      // @see http://kenwheeler.github.io/slick/#settings
 *      'clientOptions' => [
 *          'autoplay' => true,
 *      ]
 * ]); ?>
 * ```
 *
 * @author Vyacheslav Nozhenko <vv.nojenko@gmail.com>
 */
class ProductImageSlider extends Slick
{
    /**
     * @var Product
     */
    public $product;
    /**
     * @inheritdoc
     */
    public $containerOptions = ['class' => 'product-image-slider'];
    /**
     * @inheritdoc
     */
    public $itemOptions = ['class' => 'product-image-slide'];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->normalizeOptions();

        if(empty($this->items) && empty($this->product)) {
            throw new \Exception('Not allowed without items');
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $slider = Html::beginTag($this->containerTag, $this->containerOptions);

        if (!empty($this->product)) {
            $this->items = $this->renderItems();
        }

        foreach($this->items as $item) {
            $slider .= Html::tag($this->itemContainer, $item, $this->itemOptions);
        }

        $slider .= Html::endTag($this->containerTag);
        echo $slider;
        $this->registerClientScript();
    }

    /**
     * @return array
     */
    private function renderItems()
    {
        $items = [];
        foreach ($this->product->images as $productImage) {
            $items[] = $this->renderItem($productImage);
        }
        return $items;
    }

    /**
     * @param $item ProductImage
     * @return string
     */
    private function renderItem($item)
    {
        return Html::img($item->getBig(), ['alt' => $item->translation->alt]);
    }
}
