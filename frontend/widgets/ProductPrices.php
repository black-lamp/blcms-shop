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
     * Enables cache for combinations
     * @var bool
     */
    public $enableCache = false;

    /**
     * Sets cache duration
     * @var int
     */
    public $cacheDuration = 3600;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (\Yii::$app->getModule('shop')->enableCombinations && $this->product->hasCombinations()) {
            ProductCombinationAsset::register($this->getView());
            $this->renderView = 'combinations';
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
                    'enableCache' => $this->enableCache,
                    'cacheDuration' => $this->cacheDuration
                ]
            ]
        );
    }
}