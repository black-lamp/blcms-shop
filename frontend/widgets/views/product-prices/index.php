<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $product \bl\cms\shop\common\entities\Product
 * @var $form \yii\widgets\ActiveForm
 * @var $cart \bl\cms\cart\models\CartForm
 * @var $defaultCombination \bl\cms\shop\common\entities\ProductCombination
 * @var $notAvailableText string
 *
 * Ex.: echo \bl\cms\shop\frontend\widgets\ProductPrices::widget([
 *  'product' => $product,
 *  'form' => $form,
 *  'cart' => $cart,
 *  'defaultCombination' => $defaultCombination
 * ]);
 */
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;

?>

<?php if (\Yii::$app->cart->enableGetPricesFromCombinations) : ?>
    <div id="combinations-values">
        <?php foreach ($product->productAttributes as $productAttribute) : ?>
            <p class="attribute-title">
                <?= $productAttribute->translation->title; ?>
            </p>

            <?php $combinationsIds = ArrayHelper::getColumn($product->combinations, 'id'); ?>
            <?php $combinationsAttributes = $productAttribute->getProductCombinationAttributes($combinationsIds); ?>

            <?= $form->field($cart, 'attribute_value_id[]', [])->radioList(
                \yii\helpers\ArrayHelper::map($combinationsAttributes,
                    function ($model) {
                        return json_encode(['attributeId' => $model->attribute_id, 'valueId' => $model->attributeValue->id]);
                    }, function ($model) {
                        if ($model->productAttribute->type->id == \bl\cms\shop\common\entities\ShopAttributeType::TYPE_TEXTURE) {
                            return $model->attributeValue->translation->colorTexture->attributeTexture;
                        } else if ($model->productAttribute->type->id == \bl\cms\shop\common\entities\ShopAttributeType::TYPE_COLOR) {
                            return Html::tag('div', '', [
                                'style' => 'background-color: ' . $model->attributeValue->translation->colorTexture->color . ';',
                                'class' => 'attribute-color'
                            ]);
                        }
                        return $model->attributeValue->translation->value;
                    }),
                [
                    'name' => 'CartForm[attribute_value_id][' . $productAttribute->id . ']',
                    'encode' => false,
                    'item' => function ($index, $label, $name, $checked, $value) {
                        return '<label class="' . ($checked ? ' active' : '') . '">' .
                        Html::radio($name, function($model) {
                            return ($model->combination_id == $defaultCombination->id) ? 'checked' : false;
                        }, [
                            'value' => $value, 'class' => 'project-status-btn'
                        ]) . $label . '</label>';
                    },
                ]
            )->label(false); ?>
        <?php endforeach; ?>
        <?php $language = \bl\multilang\entities\Language::getCurrent(); ?>
        <p>
            <span class="price-title"><?= \Yii::t('shop', 'Price'); ?></span>:
            <span id="price" data-language-prefix="<?= $language->lang_id; ?>" data-default-value="<?= \Yii::t('shop', $notAvailableText);?>">
                <?= \Yii::$app->formatter->asCurrency((!empty($defaultCombination) ? $defaultCombination->salePrice : 0)); ?>
            </span>
        </p>
    </div>
<?php else : ?>

    <?php if (!empty($product->prices)): ?>
        <?php $id = 0; ?>
        <?php foreach ($product->prices as $key => $price): ?>
            <div class="price">
                <?= $form->field($cart, 'priceId', [
                    'enableClientValidation' => false,
                    'template' => "{input}\n{label}",
                    'inputOptions' => ['class' => 'radiobutton'],
                    'labelOptions' => ['class' => 'radiobutton']
                ])->input('radio', [
                    'id' => 'price-' . $id++,
                    'value' => $price->id,
                    'checked' => ($key == 0)
                ])->label(
                    sprintf(" %s - <span class='sum'>%s</span>",
                        $price->translation->title,
                        Yii::$app->formatter->asCurrency($price->salePrice)
                    )
                ) ?>
            </div>
        <?php endforeach; ?>
    <?php elseif (!empty($product->price)): ?>
        <div class="price">
            <span class="sum one">
                <?= Yii::$app->formatter->asCurrency($product->price) ?>
            </span>
        </div>
    <?php endif; ?>
<?php endif; ?>