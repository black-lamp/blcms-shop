<?php
namespace bl\cms\shop\frontend\widgets;

use bl\cms\cart\models\CartForm;
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\ProductCombination;
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

    /**
     * @var string
     */
    public $view;

    /**
     * @var ProductCombination
     */
    public $defaultCombination;

    public $notAvailableText = 'Not available';

    public function init()
    {
        ProductPricesAsset::register($this->getView());
    }

    public function run()
    {
        parent::run();

        return $this->render($this->view ?? 'product-prices/index',
            [
                'product' => $this->product,
                'form' => $this->form,
                'cart' => $this->cart,
                'defaultCombination' => $this->defaultCombination,
                'notAvailableText' => $this->notAvailableText
            ]);

    }
}