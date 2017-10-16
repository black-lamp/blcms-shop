<?php
namespace bl\cms\shop\frontend\controllers;

use bl\cms\shop\common\entities\Product;
use Yii;
use yii\web\Controller;
use yii\web\Response;

/**
 * @author Gutsulyak Vadim <guts.vadim@gmail.com>
 */
class PricesController extends Controller {

    public function actionAllGroups($productId, $attributeValues = null) {
        $prices = null;
        if (!empty($productId)) {
            if(!empty($attributeValues)) {
                $combination = Yii::$app->cart->getCombination($attributeValues, $productId);
                if(!empty($combination)) {
                    $prices = $combination->combinationPrices;
                }
            }
            else {
                $product = Product::find()
                    ->with('productPrices')
                    ->where(['id' => $productId])
                    ->one();

                if(!empty($product)) {
                    $prices = $product->productPrices;
                }
            }
        }

        if(Yii::$app->request->contentType == 'application/json') {
            Yii::$app->response->format = Response::FORMAT_JSON;
            // TODO: write json data fields
            return $prices;
        }

        return $this->render('all-groups', [
            'prices' => $prices
        ]);
    }

}