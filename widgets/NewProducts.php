<?php
namespace bl\cms\shop\widgets;

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

    public function run()
    {
        $products = Product::find()->orderBy('id DESC')->limit($this->num)->all();

        return $this->render('new-products', [
            'products' => $products,
        ]);
    }

}