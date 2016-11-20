<?php
namespace bl\cms\shop\widgets;

use bl\cms\cart\CartComponent;
use bl\cms\shop\common\entities\Product;
use yii\base\Widget;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * This widget shows last orders.
 *
 * Example:
 * <?= NewProducts::widget([
 * 'num' =>10
 * ]); ?>
 *
 */
class NewProducts extends Widget
{
    /**
     * @var integer
     * Number of orders which will be shown.
     */
    public $num = 10;

    /**
     * @var CartComponent
     */
    private $cart;

    public function run()
    {
        $this->cart = \Yii::$app->cart;

        $products = Product::find()->orderBy(['id' => SORT_DESC])->limit($this->num)->all();
        $showOwners = $this->cart->saveToDataBase;

        return $this->render('new-products', [
            'products' => $products,
            'showOwners' => $showOwners
        ]);
    }

}