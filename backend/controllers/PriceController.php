<?php
namespace bl\cms\shop\backend\controllers;

use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\ProductPrice;
use bl\cms\shop\common\entities\ProductPriceTranslation;
use bl\multilang\entities\Language;
use yii\helpers\Url;
use yii\web\Controller;

/**
 * @author Gutsulyak Vadim <guts.vadim@gmail.com>
 */
class PriceController extends Controller
{
    public function actionAdd($productId, $languageId) {
        $price = new ProductPrice();
        $priceTranslation = new ProductPriceTranslation();

        $product = Product::findOne($productId);
        $selectedLanguage = Language::findOne($languageId);

        if(\Yii::$app->request->isPost) {
            $post = \Yii::$app->request->post();
            if($price->load($post) && $priceTranslation->load($post)) {
                $price->product_id = $product->id;
                if($price->save()) {
                    $priceTranslation->price_id = $price->id;
                    $priceTranslation->language_id = $selectedLanguage->id;
                    if($priceTranslation->save()) {
                        $price = new ProductPrice();
                        $priceTranslation = new ProductPriceTranslation();
                    }
                }
            }
        }

        return $this->renderPartial('add', [
            'priceList' => $product->prices,
            'priceModel' => $price,
            'priceTranslationModel' => $priceTranslation,
            'product' => $product,
            'languages' => Language::findAll(['active' => true]),
            'selectedLanguage' => $selectedLanguage
        ]);

    }

    public function actionRemove($priceId) {
        ProductPrice::deleteAll(['id' => $priceId]);
        return $this->actionAdd(1, 2);
    }
}