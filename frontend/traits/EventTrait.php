<?php
namespace bl\cms\shop\frontend\traits;
use bl\cms\shop\common\entities\ViewedProduct;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
trait EventTrait
{
    /**
     * @param $productId integer
     */
    protected function getViewedProductEvent($productId)
    {
        if ($this->module->log['enabled']) {
            if (!\Yii::$app->user->isGuest) {

                $viewedProduct = ViewedProduct::find()
                    ->where(['product_id' => $productId, 'user_id' => \Yii::$app->user->id])->one();

                $ViewedProductsCount = ViewedProduct::find()
                    ->where(['user_id' => \Yii::$app->user->id])->count();

                if (empty($viewedProduct->id)) {

                    if ($ViewedProductsCount < $this->module->log['maxProducts']) {
                        $viewedProduct = new ViewedProduct([
                            'product_id' => $productId,
                            'user_id' => \Yii::$app->user->id
                        ]);

                        $viewedProduct->save();
                    }
                    else {
                        $oldViewedProduct = ViewedProduct::find()
                            ->where(['user_id' => \Yii::$app->user->id])->orderBy('id ASC')->one();
                        $oldViewedProduct->delete();
                        $viewedProduct = new ViewedProduct([
                            'product_id' => $productId,
                            'user_id' => \Yii::$app->user->id
                        ]);

                        $viewedProduct->save();
                    }
                }
                else {
                    $viewedProduct->delete();
                    $viewedProduct = new ViewedProduct([
                        'product_id' => $productId,
                        'user_id' => \Yii::$app->user->id
                    ]);

                    $viewedProduct->save();
                }
            }
        }

    }

}
