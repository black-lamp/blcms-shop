<?php
/**
 * If Cart module $enableGetPricesFromCombinations property is true
 * and if there are product combinations attributes this view will be displayed.
 *
 * @var $product \bl\cms\shop\common\entities\Product
 * @var $form \yii\widgets\ActiveForm
 * @var $cart \bl\cms\cart\models\CartForm
 * @var $defaultCombination \bl\cms\shop\common\entities\ProductCombination
 * @var $notAvailableText string
 *
 * @var $product ->productAttributes ShopAttribute[] Attributes that are present in the combinations of this product
 *
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
use bl\cms\shop\common\entities\ShopAttributeType;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;

global $globalDefaultCombination;
$globalDefaultCombination = $defaultCombination;
?>

<div id="combinations-values" data-product-id="<?= $product->id; ?>">

    <?php foreach ($product->productAttributes as $productAttribute) : ?>
        <p class="attribute-title">
            <?= $productAttribute->translation->title; ?>
        </p>

        <?php $combinationsIds = ArrayHelper::getColumn($product->combinations, 'id'); ?>
        <?php $combinationsAttributes = $productAttribute->getProductCombinationAttributes($combinationsIds); ?>

        <?php if ($productAttribute->type_id == ShopAttributeType::TYPE_DROP_DOWN_LIST) : ?>

            <?= $form->field($cart, 'attribute_value_id[' . $product->id . ']', [])
                ->dropDownList(ArrayHelper::map($combinationsAttributes,
                    function ($model) {
                        return json_encode(['attributeId' => $model->attribute_id, 'valueId' => $model->attributeValue->id]);
                    },
                    function ($model) {
                        if ($model->productAttribute->type->id == ShopAttributeType::TYPE_TEXTURE) {
                            return $model->attributeValue->translation->colorTexture->attributeTexture;
                        } else if ($model->productAttribute->type->id == ShopAttributeType::TYPE_COLOR) {
                            return Html::tag('div', '', [
                                'style' => 'background-color: ' . $model->attributeValue->translation->colorTexture->color . ';',
                                'class' => 'attribute-color',
                            ]);
                        }
                        return $model->attributeValue->translation->value;
                    }),
                    [
                        'name' => 'CartForm[attribute_value_id][' . $product->id . '-' . $productAttribute->id . ']',
                        'encode' => false,
//                        'item' => function ($index, $label, $name, $checked, $value) {
//
//                            if (!empty($GLOBALS['globalDefaultCombination'])) {
//                                foreach ($GLOBALS['globalDefaultCombination']->shopProductCombinationAttributes as $attribute) {
//                                    $serialized = json_encode([
//                                        'attributeId' => $attribute->attribute_id,
//                                        'valueId' => $attribute->attribute_value_id]);
//                                    if ($serialized == $value) $checked = true;
//                                }
//                            }
//                            return '<label class="btn btn-default' . ($checked ? ' active' : '') . '">' .
//                            Html::radio($name, $checked, ['value' => $value, 'class' => 'radiobutton']) . $label . '</label>';
//                        },
                    ]
                )->label(false); ?>

        <?php else : ?>

            <?php $checked = true;
            echo $form->field($cart, 'attribute_value_id[' . $product->id . ']', [])
                ->radioList(ArrayHelper::map($combinationsAttributes,
                    function ($model) {
                        return json_encode(['attributeId' => $model->attribute_id, 'valueId' => $model->attributeValue->id]);
                    },
                    function ($model) {
                        if ($model->productAttribute->type->id == ShopAttributeType::TYPE_TEXTURE) {
                            return $model->attributeValue->translation->colorTexture->attributeTexture;
                        } else if ($model->productAttribute->type->id == ShopAttributeType::TYPE_COLOR) {
                            return Html::tag('div', '', [
                                'style' => 'background-color: ' . $model->attributeValue->translation->colorTexture->color . ';',
                                'class' => 'attribute-color',
                            ]);
                        }
                        return $model->attributeValue->translation->value;
                    }),
                    [
                        'name' => 'CartForm[attribute_value_id][' . $product->id . '-' . $productAttribute->id . ']',
                        'encode' => false,
                        'item' => function ($index, $label, $name, $checked, $value) {

                            if (!empty($GLOBALS['globalDefaultCombination'])) {
                                foreach ($GLOBALS['globalDefaultCombination']->shopProductCombinationAttributes as $attribute) {
                                    $serialized = json_encode([
                                        'attributeId' => $attribute->attribute_id,
                                        'valueId' => $attribute->attribute_value_id]);
                                    if ($serialized == $value) $checked = true;
                                }
                            }
                            return '<label class="btn btn-default' . ($checked ? ' active' : '') . '">' .
                            Html::radio($name, $checked, ['value' => $value, 'class' => 'radiobutton']) . $label . '</label>';
                        },
                    ]
                )->label(false); ?>
        <?php endif; ?>
    <?php endforeach; ?>

    <?= $this->render('sum', [
        'defaultCombination' => $defaultCombination,
    ]); ?>
</div>
