<?php
/**
 * The block where price sum will be shown.
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $defaultCombination \bl\cms\shop\common\entities\ProductCombination|null
 * @var $priceModel \bl\cms\shop\common\entities\ProductPrice|null
 */

if (!empty($defaultCombination)) {
    $oldPrice = $defaultCombination->oldPrice ?? 0;
    $newPrice = $defaultCombination->salePrice ?? 0;
}
else if (!empty($priceModel)) {
    $oldPrice = $priceModel->getPrice() ?? 0;
    $newPrice = $priceModel->getSalePrice() ?? 0;
}
else {
    $newPrice = $basePrice ?? 0;
}

?>

<div class="price">
    <p class="price-title"><?= \Yii::t('shop', 'Price'); ?>:</p>

    <!--OLD PRICE-->
    <p id="oldPrice" class="old-sum">
        <?= (!empty($oldPrice)) ? \Yii::$app->formatter->asCurrency($oldPrice) : ''; ?>
    </p>
    <!--NEW PRICE-->
    <p id="newPrice" class="sum one"
       data-sum="<?= $newPrice; ?>"
       data-currency-code="<?= \Yii::$app->formatter->numberFormatterSymbols[NumberFormatter::CURRENCY_SYMBOL]; ?>">
        <?= \Yii::$app->formatter->asCurrency($newPrice); ?>
    </p>
</div>