<?php
namespace bl\cms\shop\frontend\widgets\traits;

use bl\cms\shop\common\entities\ProductPrice;
use Yii;
use yii\helpers\Json;

/**
 * This trait must be used in ProductController for ajax requests from ProductPricesWidget.
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
trait ProductPricesTrait
{

    /**
     * @param $values
     * @param $productId
     * @return int|string
     */
    public function actionGetProductCombination($values, $productId) {
        $values = Json::decode($values);
        $combination = \Yii::$app->cart->getCombination($values, $productId);
        if (!empty($combination)) {
            $array = [
                'image' => $combination->images[0]->productImage->thumb ?? '',
                'oldPrice' => $combination->oldPrice ?? '',
                'newPrice' => $combination->salePrice ?? '',
                'articulus' => $combination->articulus ?? ''
            ];
        }
        else return 0;
        return Json::encode($array);
    }


    public function actionGetProductPrice(int $priceId) {
        $productPrice = ProductPrice::findOne($priceId);

        $prices = [
            'price' => $productPrice->getPrice(),
            'salePrice' => $productPrice->getSalePrice()
        ];
        return Json::encode($prices);
    }
}