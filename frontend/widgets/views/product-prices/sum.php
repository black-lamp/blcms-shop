<?php
/**
 * The block where price sum will be shown.
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $defaultCombination \bl\cms\shop\common\entities\ProductCombination|null
 * @var $priceModel \bl\cms\shop\common\entities\ProductPrice|null
 */

if (!empty($defaultCombination)) {
    $oldPrice = $defaultCombination->price->oldPrice ?? 0;
    $newPrice = $defaultCombination->price->discountPrice ?? 0;
}
else {
    $newPrice = $basePrice ?? 0;
}
?>

<div>
    <h3 class="title">
        <?= Yii::t('frontend.product.card', 'Price') ?>
    </h3>
    <p id="oldPrice" class="old-sum">
        <?= (!empty($oldPrice)) ? Yii::$app->formatter->asCurrency($oldPrice) : ''; ?>
    </p>
    <p id="newPrice" class="sum one"
       data-sum="<?= $newPrice; ?>"
       data-currency-code="<?= Yii::$app->formatter->numberFormatterSymbols[NumberFormatter::CURRENCY_SYMBOL]; ?>">
        <?= Yii::$app->formatter->asCurrency($newPrice) ?>
    </p>
</div>