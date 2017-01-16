<?php
namespace bl\cms\shop\backend\controllers;
use bl\cms\shop\backend\components\events\PriceEvent;
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\ProductPrice;
use bl\cms\shop\common\entities\ProductPriceTranslation;
use bl\multilang\entities\Language;
use yii\web\Controller;

/**
 * @author Gutsulyak Vadim <guts.vadim@gmail.com>
 */
class PriceController extends Controller
{

    /**
     * Event is triggered after creating new price.
     * Triggered with bl\cms\shop\backend\components\events\PriceEvent.
     */
    const EVENT_AFTER_SAVE_PRICE = 'afterSavePrice';

    /**
     * Event is triggered after creating new price.
     * Triggered with bl\cms\shop\backend\components\events\PriceEvent.
     */
    const EVENT_AFTER_DELETE_PRICE = 'afterDeletePrice';

    /**
     * @param $productId
     * @param $languageId
     * @return string
     */
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

                        $this->trigger(self::EVENT_AFTER_SAVE_PRICE, new PriceEvent([
                            'priceId' => $price->id,
                            'userName' => \Yii::$app->user->identity->username,
                        ]));

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

    public function actionRemove($priceId, $productId, $languageId) {
        $price = ProductPrice::findOne($priceId);

        if (!empty($price)) {
            $price->delete();
        }

        $this->trigger(self::EVENT_AFTER_DELETE_PRICE, new PriceEvent([
            'priceId' => $priceId,
            'userName' => \Yii::$app->user->identity->username,
        ]));

        return $this->actionAdd($productId, $languageId);
    }
}