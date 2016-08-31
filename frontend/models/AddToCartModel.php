<?php
namespace bl\cms\shop\frontend\models;

use Yii;
use yii\base\Model;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class AddToCartModel extends Model
{
    public $price_id;
    public $count = 1;

    public function rules()
    {
        // TODO: validation rules
        return [
            [['price_id', 'count'], 'integer']
        ];
    }

    public function add() {
        $cart = Yii::$app->session->get('cart');

        if(empty($cart)) {
            $cart = [];
        }

        if(!empty($cart[$this->price_id])) {
            $cart[$this->price_id] += $this->count;
        }
        else {
            $cart[$this->price_id] = $this->count;
        }

        Yii::$app->session->set('cart', $cart);

        return true;
    }
}