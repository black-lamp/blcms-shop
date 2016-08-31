<?php
namespace bl\cms\shop\frontend\models;

use bl\cms\shop\common\entities\ProductPrice;
use yii\base\Model;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class CartItem extends Model
{
    /**
     * @var ProductPrice
     */
    public $price;
    /**
     * @var integer
     */
    public $count;
}