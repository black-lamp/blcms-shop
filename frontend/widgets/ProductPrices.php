<?php
namespace bl\cms\shop\frontend\widgets;

use bl\cms\shop\frontend\widgets\assets\ProductCombinationAsset;
use yii\base\Widget;
use yii\widgets\ActiveForm;
use bl\cms\cart\models\CartForm;
use bl\cms\shop\common\entities\{
    Product, Combination
};
use bl\cms\shop\frontend\widgets\assets\ProductPricesAsset;

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
     * @var Combination
     */
    public $defaultCombination;

    /**
     * If there is not combination this text will be displayed.
     * @var string
     */
    public $notAvailableText = 'Not available';

    /**
     * Path to widget index view
     * @var string
     */
    public $view;

    /**
     * @var string
     */
    public $renderView;

    /**
     * @var bool
     */
    public $showCounter = true;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (\Yii::$app->cart->enableGetPricesFromCombinations && !empty($this->product->productAttributes)) {
            ProductCombinationAsset::register($this->getView());
            $this->renderView = 'combinations';
        } elseif (
            (\Yii::$app->cart->enableGetPricesFromCombinations
                && empty($this->product->productAttributes)
                && !empty($this->product->prices)) ||
            (!\Yii::$app->cart->enableGetPricesFromCombinations
                && !empty($this->product->prices))
        ) {
            ProductPricesAsset::register($this->getView());
            $this->renderView = 'prices';
        } else {
            $this->renderView = 'base-price';
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        parent::run();

        return $this->render($this->view ?? 'product-prices/index',
            [
                'renderView' => $this->renderView,
                'params' => [
                    'product' => $this->product,
                    'form' => $this->form,
                    'cart' => new CartForm(),
                    'defaultCombination' => $this->defaultCombination,
                    'notAvailableText' => $this->notAvailableText,
                    'showCounter' => $this->showCounter,
                ]
            ]
        );
    }
}