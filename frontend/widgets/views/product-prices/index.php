<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $product \bl\cms\shop\common\entities\Product
 * @var $form \yii\widgets\ActiveForm
 * @var $cart \bl\cms\cart\models\CartForm
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

            <?= $form->field($cart, 'attribute_value_id[]')->radioList(
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
                ['name' => 'CartForm[attribute_value_id][' . $productAttribute->id . ']', 'encode' => false]
            )->label(false); ?>
        <?php endforeach; ?>
        <?php $language = \bl\multilang\entities\Language::getCurrent(); ?>
        <p>
            <span class="price-title"><?= \Yii::t('shop', 'Price'); ?></span>:
            <span id="price" data-language-prefix="<?= $language->lang_id; ?>"></span>
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