<?php
namespace bl\cms\shop\frontend\widgets;

use bl\cms\cart\models\CartForm;
use bl\cms\shop\common\entities\CombinationAttribute;
use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\ShopAttribute;
use bl\cms\shop\common\entities\ShopAttributeType;
use Yii;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * Renders product 'add to cart' form with price, attributes.
 *
 * Example:
 * ```php
 *  <?php $attributes = \bl\cms\shop\frontend\widgets\AttributesWidget::begin([
 *      'product' => $model,
 *  ]); ?>
 *  <?= $attributes->renderPrice(); ?>
 *  <?= $attributes->renderAttributes(); ?>
 *  <?= $attributes->renderSubmitButton(); ?>
 *  <?php $attributes->end(); ?>
 * ```
 *
 * @author Vyacheslav Nozhenko <vv.nojenko@gmail.com>
 */
class AttributesWidget extends Widget
{
    CONST DEFAULT_PRODUCT_COUNT = 1;

    /**
     * @var Product
     */
    public $product;
    /**
     * @var ActiveForm
     */
    public $form;
    /**
     * @var CartForm
     */
    public $cartForm;

    /**
     * @var array the HTML attributes of 'form' block.
     */
    public $options = [];
    /**
     * @var array the HTML attributes for 'prices' container block.
     */
    public $priceContainerOptions = [];
    /**
     * @var array the HTML attributes for 'default price' block.
     */
    public $priceOptions = [];
    /**
     * @var array the HTML attributes for 'discount price' block.
     */
    public $discountPriceOptions = [];

    /**
     * @var array the HTML attributes for 'attributes' container block.
     */
    public $attributeContainerOptions = [];
    /**
     * @var array the HTML attributes for 'attribute title' block.
     */
    public $attributeTitleOptions = [];
    /**
     * @var array the HTML attributes for 'submit button'.
     */
    public $submitButtonOptions = ['class' => 'btn btn-success'];

    /**
     * @var array
     */
    public $textureInputOptions = ['class' => 'texture'];

    /**
     * @var array
     */
    public $labelOptions = [];

    /**
     * @var string the default CSS classes.
     */
    public $skuCssClass = 'product-sku';
    private $_formClass = 'product-price-form';
    private $_attributesContainerClass = 'product-attributes';
    private $_priceClass = 'product-price';
    private $_discountPriceClass = 'product-discount-price';

    /**
     * @inheritDoc
     */
    public function init()
    {
        if (empty($this->product)) {
            return false;
        }

        $this->cartForm = new CartForm();

        Html::addCssClass($this->options, $this->_formClass);
        Html::addCssClass($this->priceOptions, $this->_priceClass);
        Html::addCssClass($this->discountPriceOptions, $this->_discountPriceClass);
        Html::addCssClass($this->attributeContainerOptions, $this->_attributesContainerClass);

        $this->form = ActiveForm::begin([
            'action' => ['/cart/cart/add'],
            'options' => $this->options
        ]);
        echo $this->renderHiddenInputs();
    }

    /**
     * @inheritDoc
     */
    public function run()
    {
        $this->form->end();
        $this->registerGetCombinationScript();
    }

    /**
     * Renders hidden inputs.
     *
     * @return string
     */
    private function renderHiddenInputs()
    {
        $items = $this->form->field($this->cartForm, 'productId')->hiddenInput(['value' => $this->product->id])
            ->label(false);
        $items .= $this->form->field($this->cartForm, 'count')->hiddenInput(['value' => self::DEFAULT_PRODUCT_COUNT])
            ->label(false);

        return $items;
    }

    /**
     * Renders 'prices' block.
     *
     * @return string
     */
    public function renderPrice()
    {
        if (empty($this->product)) {
            return false;
        }

        $items = '';
        if (!empty($this->priceContainerOptions)) {
            $items = Html::beginTag('div', $this->priceContainerOptions);
        }

        if (!empty($this->product->defaultCombination)) {
            $items .= $this->renderCombinationPriceItem();
        }
        else {
            $items .= $this->renderDefaultPriceItem();
        }

        if (!empty($this->priceContainerOptions)) {
            $items .= Html::endTag('div');
        }

        return $items;
    }

    /**
     * Renders 'attributes' block.
     * TODO: refactoring
     *
     * @return string
     */
    public function renderAttributes()
    {
        if (empty($this->product->combinations)) {
            return false;
        }

        $items = '';
        if (!empty($this->attributeContainerOptions)) {
            $items .= Html::beginTag('div', $this->attributeContainerOptions);
        }

        $attributeTitle = '';
        foreach ($this->product->combinations as $combination) {
            $currentAttributeTitle = (!empty($combination->combinationAttributes[0]))
                ? $combination->combinationAttributes[0]->productAttribute->translation->title
                : '';
            if ($attributeTitle != $currentAttributeTitle) {
                $attributeTitle = $currentAttributeTitle;

                $items .= Html::tag('p', $attributeTitle . ':', $this->attributeTitleOptions);
                $items .= $this->renderAttributeItem($combination->combinationAttributes);
            }
        }

        if (!empty($this->attributeContainerOptions)) {
            $items .= Html::endTag('div');
        }

        return $items;
    }

    /**
     * Renders 'submit button'.
     *
     * @return string
     */
    public function renderSubmitButton()
    {
        return Html::submitButton(Yii::t('shop', 'To cart'), $this->submitButtonOptions);
    }


    /**
     * TODO: render the color input, refactoring.
     *
     * @param CombinationAttribute[] $attributes
     * @return string
     */
    private function renderAttributeItem($attributes)
    {
        $item = '';
        $productId = $this->product->id;

        $productAttribute = (!empty($attributes[0]->productAttribute))
            ? $attributes[0]->productAttribute
            : new ShopAttribute();
        $attributeType = $productAttribute->type_id;

        $combinationsAttributes = $productAttribute->getProductCombinationAttributes(ArrayHelper::getColumn(
            $this->product->combinations, 'id'
        ));

        $attributesItems = ArrayHelper::map($combinationsAttributes,
            function ($model) {
                /** @var CombinationAttribute $model */
                return json_encode(['attributeId' => $model->attribute_id, 'valueId' => $model->productAttributeValue->id]);
            },
            function ($model) {
                /** @var CombinationAttribute $model */
                return $model->productAttributeValue->translation->value;
            }
        );

        switch ($attributeType) {
            case ShopAttributeType::TYPE_DROP_DOWN_LIST:
                $item .= $this->form->field($this->cartForm, 'attribute_value_id')
                    ->dropDownList($attributesItems, [
                        'name' => "CartForm[attribute_value_id][$productId-$productAttribute->id]"
                    ])
                    ->label(false);
                break;

            case ShopAttributeType::TYPE_RADIO_BUTTON:
                $item .= $this->form->field($this->cartForm, "attribute_value_id")
                    ->radioList($attributesItems, [
                        'name' => "CartForm[attribute_value_id][$productId-$productAttribute->id]",
                        'item' => function ($index, $label, $name, $checked, $value) use ($combinationsAttributes) {
                            $checked = $combinationsAttributes[$index]->combination->default;

                            return Html::label(Html::radio($name, $checked, ['value' => $value])
                                . $label
                            );
                        }
                    ])
                    ->label(false);
                break;

            case ShopAttributeType::TYPE_COLOR:
                $item .= $this->form->field($this->cartForm, "attribute_value_id")
                    ->radioList(ArrayHelper::map($combinationsAttributes,
                        function ($model) {
                            /** @var CombinationAttribute $model */
                            return json_encode(['attributeId' => $model->attribute_id, 'valueId' => $model->productAttributeValue->id]);
                        },
                        function ($model) {
                            /** @var CombinationAttribute $model */
                            return $model->productAttributeValue->translation->colorTexture->color;
                        }), [
                        'name' => "CartForm[attribute_value_id][$productId-$productAttribute->id]",
                        'item' => function ($index, $label, $name, $checked, $value) use ($combinationsAttributes) {
                            $checked = $combinationsAttributes[$index]->combination->default;

                            $options = $this->textureInputOptions;
                            Html::addCssStyle($options, ['background-color' => $label]);

                            return Html::label(
                                Html::radio($name, $checked, ['value' => $value]) . Html::tag('span', '', $options),
                                null, $this->labelOptions
                            );
                        }
                    ])
                    ->label(false);
                break;

            case ShopAttributeType::TYPE_TEXTURE:
                $item .= $this->form->field($this->cartForm, "attribute_value_id")
                    ->radioList(ArrayHelper::map($combinationsAttributes,
                        function ($model) {
                            /** @var CombinationAttribute $model */
                            return json_encode(['attributeId' => $model->attribute_id, 'valueId' => $model->productAttributeValue->id]);
                        },
                        function ($model) {
                            /** @var CombinationAttribute $model */
                            return $model->productAttributeValue->translation->colorTexture->getTextureFile();
                        }), [
                        'name' => "CartForm[attribute_value_id][$productId-$productAttribute->id]",
                        'item' => function ($index, $label, $name, $checked, $value) use ($combinationsAttributes) {
                            $checked = $combinationsAttributes[$index]->combination->default;

                            $options = $this->textureInputOptions;
                            Html::addCssStyle($options, ['background-image' => "url($label)"]);

                            return Html::label(
                                Html::radio($name, $checked, ['value' => $value]) . Html::tag('span', '', $options),
                                null, $this->labelOptions
                            );
                        }
                    ])
                    ->label(false);
                break;
        }

        return $item;
    }

    /**
     * Renders 'price item' using product price from default combination.
     *
     * @return string
     */
    private function renderCombinationPriceItem() {
        $item = Html::tag('strong',
            Yii::$app->formatter->asCurrency($this->product->defaultCombination->price->discountPrice),
            $this->discountPriceOptions
        );

        if (empty($this->product->defaultCombination->price->discount_type_id)) {
            Html::addCssStyle($this->priceOptions, ['display' => 'none']);
        }

        $item .= Html::tag('strike',
            Yii::$app->formatter->asCurrency($this->product->defaultCombination->price->oldPrice),
            $this->priceOptions
        );

        return $item;
    }

    /**
     * Renders 'price item' using default product price.
     *
     * @return string
     */
    private function renderDefaultPriceItem() {
        $item = Html::tag('strong',
            Yii::$app->formatter->asCurrency($this->product->getDiscountPrice()),
            $this->discountPriceOptions
        );

        if (!empty($this->product->price->discount_type_id)) {
            $item .= Html::tag('strike',
                Yii::$app->formatter->asCurrency($this->product->getOldPrice()),
                $this->priceOptions
            );
        }

        return $item;
    }

    /**
     * Registers necessary JavaScript.
     */
    private function registerGetCombinationScript() {
        $js = <<<JS
                
var addToCartForms = $('.$this->_formClass');

addToCartForms.change(function() {
    var productId = $(this).find('#cartform-productid').val();
    var price = $(this).find('.$this->_priceClass');
    var discountPrice = $(this).find('.$this->_discountPriceClass');
    var checkedValues = $(this).find('input:checked');
    var selectedValues = $(this).find('option:selected');
    var sliderThumbs = $('#productImageSliderThumbs');
    var sku = $('.$this->skuCssClass');

    var values = [];
    for (var i = 0; i < checkedValues.length; i++) {
        values[i] = checkedValues[i].value;
    }
    for (var j = 0; j < selectedValues.length; j++) {
        values[checkedValues.length + j] = $(selectedValues[j]).val();
    }
    
    values = JSON.stringify(values);
  
    $.ajax({
        type: "GET",
        url: '/shop/product/get-product-combination',
        data: {
            values: values,
            productId: productId,
            currencyFormatting: true
        },
        success: function (data) {
            data = JSON.parse(data);
            
            discountPrice.html(data.newPrice);
          
            if (data.oldPrice == data.newPrice) {
                price.hide(150);
            } else {
                price.html(data.oldPrice);
                price.show(300);
                price.css('display', 'block');
            }
            
            sku.html(data.sku);
            $(sliderThumbs).find("img[src='" + data.image + "']").click();
        },
        error: function (data) {
            console.log(data);
        }
    });
});

JS;

        $this->view->registerJs($js, View::POS_END, __CLASS__);
    }
}
