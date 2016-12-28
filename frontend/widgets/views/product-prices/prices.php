<?php
/**
 * If Cart module $enableGetPricesFromCombinations property is true
 * and there are not combination attributes but there are product prices
 * or Cart module $enableGetPricesFromCombinations property is false and there are product prices
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
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
?>

<div class="prices-block" data-product-id="<?= $product->id; ?>">
    <?php $cart->priceId = $product->prices[0]->id; ?>
    <?= $form->field($cart, 'priceId', [])
        ->radioList(ArrayHelper::map($product->prices, 'id', 'translation.title'), [
            'item' => function ($index, $label, $name, $checked, $value) {
                return '<label class="btn btn-default' . ($checked ? ' active' : '') . '">' .
                Html::radio($name, $checked, ['value' => $value, 'class' => 'radiobutton']) . $label . '</label>';
            }
        ])->label(false); ?>

    <?= $this->render('sum', [
        'priceModel' => $product->prices[0]
    ]); ?>
</div>