<?php
namespace bl\cms\shop\frontend\widgets;

use bl\cms\cart\models\CartForm;
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\frontend\widgets\assets\ProductPricesAsset;
use yii\base\Widget;
use yii\widgets\ActiveForm;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class ProductPrices extends Widget
{

    /**
     * @var Product
     */
    public $product;

    /**
     * @var ActiveForm
     */
    public $form;

    /**
     * @var CartForm
     */
    public $cart;

    public function init()
    {
        ProductPricesAsset::register($this->getView());
    }

    public function run()
    {
        parent::run();

        return $this->render('product-prices/index',
            [
                'product' => $this->product,
                'form' => $this->form,
                'cart' => $this->cart
            ]);

    }
}