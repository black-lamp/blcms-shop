<?php
/**
 * The block where price sum will be shown.
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $defaultCombination \bl\cms\shop\common\entities\Combination|null
 * @var $priceModel \bl\cms\shop\common\entities\Price|null
 *
 * @var $oldPrice string
 * @var $newPrice string
 */

if (!empty($defaultCombination)) {
    $oldPrice = $defaultCombination->price->oldPrice ?? 0;
    $newPrice = $defaultCombination->price->discountPrice ?? 0;
}
?>

<div>
    <p class="sum">
        <span class="price-title"><?= Yii::t('shop', 'Price') ?>:</span>
        <span id="newPrice"
              data-sum="<?= $newPrice; ?>"
              data-currency-code="<?= Yii::$app->formatter->numberFormatterSymbols[NumberFormatter::CURRENCY_SYMBOL]; ?>">
            <?= Yii::$app->formatter->asCurrency($newPrice) ?>
        </span>

        <span id="oldPrice" class="old-sum">
            <?= (!empty($oldPrice)) ? Yii::$app->formatter->asCurrency($oldPrice) : ''; ?>
        </span>
    </p>

</div>