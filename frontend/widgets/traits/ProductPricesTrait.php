<?php
namespace bl\cms\shop\frontend\widgets\traits;

use bl\cms\cart\CartComponent;
use Yii;
use yii\helpers\Json;

/**
 * This trait must be used in ProductController for ajax requests from ProductPricesWidget, AttributesWidget.
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
trait ProductPricesTrait
{

    /**
     * @param $values
     * @param $productId
     * @param bool $currencyFormatting
     * @return int|string
     */
    public function actionGetProductCombination($values, $productId, $currencyFormatting = false) {
        /** @var CartComponent $cart */
        $cart = \Yii::$app->cart;

        $values = Json::decode($values);
        $combination = $cart->getCombination($values, $productId);
        if (!empty($combination)) {
            $oldPrice = $combination->price->oldPrice ?? '';
            $newPrice = $combination->price->discountPrice ?? '';
            if ($currencyFormatting) {
                $oldPrice = Yii::$app->formatter->asCurrency($oldPrice);
                $newPrice = Yii::$app->formatter->asCurrency($newPrice);
            }

            $array = [
                'image' => $combination->images[0]->productImage->thumb ?? '',
                'oldPrice' => $oldPrice,
                'newPrice' => $newPrice,
                'sku' => $combination->sku ?? ''
            ];
        }
        else return 0;

        return Json::encode($array);
    }
}