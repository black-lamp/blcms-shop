<?php
namespace bl\cms\shop\frontend\models;

use bl\cms\shop\common\entities\ProductPrice;
use yii\base\Model;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class Cart extends Model
{

    /**
     * @var CartItem[]
     */
    public $items = [];
    public $sum;

    public function load($cart, $formName = null)
    {
        if (!empty($cart)) {

            foreach ($cart as $priceId => $count) {
                $price = ProductPrice::findOne($priceId);

                if(!empty($price)) {
                    $cartItem = new CartItem();
                    $cartItem->price = $price;
                    $cartItem->count = $count;

                    $this->items[] = $cartItem;
                    $this->sum += $price->salePrice * $count;
                }
            }
        }

        return true;
    }


}