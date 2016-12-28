<?php
/**
 * If Cart module $enableGetPricesFromCombinations property is false
 * or there are not product combinations and there not product prices
 * this view will be displayed.
 *
 * @var $product \bl\cms\shop\common\entities\Product
 * @var $form \yii\widgets\ActiveForm
 * @var $cart \bl\cms\cart\models\CartForm
 * @var $defaultCombination \bl\cms\shop\common\entities\ProductCombination
 * @var $notAvailableText string
 *
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
?>

<div class="prices-block">
        <?= $this->render('prices', [
            'basePrice' => $product->price ?? 0
        ]); ?>
</div>
