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
    CONST NOT_FOUND_COMBINATION = 0;
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
     * @var array
     */
    public $formConfig = ['action' => ['/cart/cart/add']];
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
    public $attributeTitleOptions = ['class' => 'attribute-title'];
    /**
     * @var array the HTML attributes for 'submit button'.
     */
    public $submitButtonOptions = ['class' => 'btn btn-success'];

    /**
     * @var array the HTML attributes for 'texture input'.
     */
    public $textureInputOptions = ['class' => 'texture'];

    /**
     * @var array the additional configurations for the field object. These are properties of [[ActiveField]]
     * or a subclass, depending on the value of [[fieldClass]].
     */
    public $listInputOptions = ['inputOptions' => ['class' => 'form-control']];

    /**
     * @var array
     */
    public $labelOptions = [];
    /**
     * @var string the message displayed if the combination was not found.
     */
    public $notAvailableMessage = '';
    /**
     * @var array the HTML attributes for 'notAvailableMessage' block.
     */
    public $notAvailableMessageOptions = ['style' => ['display' => 'none']];

    /**
     * @var string the default CSS classes.
     */
    public $skuCssClass = 'product-sku';
    public $statusCssClass = 'product-status';
    private $_formClass = 'product-price-form';
    private $_attributesContainerClass = 'product-attributes';
    private $_priceClass = 'product-price';
    private $_discountPriceClass = 'product-discount-price';
    private $_notAvailableMessageClass = 'not-available-message';

    /**
     * @inheritDoc
     */
    public function init()
    {
        if (empty($this->product)) {
            return false;
        }

        $this->cartForm = new CartForm();

        if (empty($this->notAvailableMessage)) {
            $this->notAvailableMessage = Yii::t('shop', 'This product is not available in this configuration');
        }

        Html::addCssClass($this->formConfig['options'], $this->_formClass);
        Html::addCssClass($this->priceOptions, $this->_priceClass);
        Html::addCssClass($this->discountPriceOptions, $this->_discountPriceClass);
        Html::addCssClass($this->attributeContainerOptions, $this->_attributesContainerClass);
        Html::addCssClass($this->notAvailableMessageOptions, $this->_notAvailableMessageClass);


        $this->form = ActiveForm::begin($this->formConfig);
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
        $items = $this->form->field($this->cartForm, 'productId')
            ->hiddenInput(['value' => $this->product->id])
            ->label(false);
        $items .= $this->form->field($this->cartForm, 'count')
            ->hiddenInput(['value' => self::DEFAULT_PRODUCT_COUNT])
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

        foreach ($this->product->productAttributes as $attribute) {
            $items .= Html::tag('p', $attribute->translation->title . ':', $this->attributeTitleOptions);
            $items .= $this->renderAttributeItem($attribute);
        }

        if (!empty($this->attributeContainerOptions)) {
            $items .= Html::endTag('div');
        }

        $items .= Html::tag('p', $this->notAvailableMessage, $this->notAvailableMessageOptions);

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
     * Renders 'attribute item' input
     *
     * @param ShopAttribute $attribute
     * @return string
     */
    private function renderAttributeItem($attribute)
    {
        $item = '';
        $productId = $this->product->id;

        $combinationsAttributes = $attribute->getProductCombinationAttributes(ArrayHelper::getColumn(
            $this->product->combinations, 'id'
        ));
        $attributeType = $attribute->type_id;

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
                $item .= $this->form->field($this->cartForm, 'attribute_value_id', $this->listInputOptions)
                    ->dropDownList($attributesItems, [
                        'name' => "CartForm[attribute_value_id][$productId-$attribute->id]"
                    ])
                    ->label(false);
                break;

            case ShopAttributeType::TYPE_RADIO_BUTTON:
                $item .= $this->form->field($this->cartForm, "attribute_value_id")
                    ->radioList($attributesItems, [
                        'name' => "CartForm[attribute_value_id][$productId-$attribute->id]",
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
                            return json_encode(['attributeId' => $model->attribute_id, 'valueId' => $model->attribute_value_id]);
                        },
                        function ($model) { return $model; }),
                        [
                            'name' => "CartForm[attribute_value_id][$productId-$attribute->id]",
                            'item' => function ($index, $label, $name, $checked, $value) {
                                /** @var CombinationAttribute $model */
                                $model = $label;

                                if (!empty($this->product->defaultCombination)) {
                                    // TODO: optimize this
                                    foreach ($this->product->defaultCombination->combinationAttributes as $attr) {
                                        $serialized = json_encode([
                                            'attributeId' => $attr->attribute_id,
                                            'valueId' => $attr->attribute_value_id
                                        ]);
                                        if ($serialized == $value) {
                                            $checked = true;
                                        };
                                    }
                                }

                                $options = $this->textureInputOptions;
                                $labelOptions = $this->labelOptions;
                                $title = $model->productAttributeValue->translation->colorTexture->title ?? '';
                                $color = $model->productAttributeValue->translation->colorTexture->color;

                                $options['title'] = $title;
                                Html::addCssStyle($options, ['background-color' => $color]);
                                Html::addCssClass($labelOptions, 'color');

                                return Html::label(
                                    Html::radio($name, $checked, ['value' => $value]) . Html::tag('span', '', $options),
                                    null, $labelOptions
                                );
                            }
                        ]
                    )->label(false);
                break;

            case ShopAttributeType::TYPE_TEXTURE:
                $item .= $this->form->field($this->cartForm, "attribute_value_id")
                    ->radioList(ArrayHelper::map($combinationsAttributes,
                        function ($model) {
                            /** @var CombinationAttribute $model */
                            return json_encode(['attributeId' => $model->attribute_id, 'valueId' => $model->attribute_value_id]);
                        },
                        function ($model) { return $model; }),
                        [
                            'name' => "CartForm[attribute_value_id][$productId-$attribute->id]",
                            'item' => function ($index, $label, $name, $checked, $value) use ($attribute) {
                                /** @var CombinationAttribute $model */
                                $model = $label;

                                if (!empty($this->product->defaultCombination)) {
                                    // TODO: optimize this
                                    foreach ($this->product->defaultCombination->combinationAttributes as $attr) {
                                        $serialized = json_encode([
                                            'attributeId' => $attr->attribute_id,
                                            'valueId' => $attr->attribute_value_id
                                        ]);
                                        if ($serialized == $value) {
                                            $checked = true;
                                        };
                                    }
                                }

                                $serialized = json_encode([
                                    'attributeId' => $model->id,
                                    'valueId' => $model->attribute_value_id
                                ]);
                                if ($serialized == $value)  {
                                    $checked = true;
                                }


                                echo "$checked <br>";

                                $options = $this->textureInputOptions;
                                $labelOptions = $this->labelOptions;
                                $title = $model->productAttributeValue->translation->colorTexture->title;
                                $texture = $model->productAttributeValue->translation->colorTexture->getTextureFile();

                                $options['title'] = $title;
                                Html::addCssStyle($options, ['background-image' => "url($texture)"]);
                                Html::addCssClass($labelOptions, 'texture');

                                return Html::label(
                                    Html::radio($name, $checked, ['value' => $value]) . Html::tag('span', '', $options),
                                    null, $labelOptions
                                );
                            }
                        ]
                    )->label(false);
                break;
        }

        return $item;
    }


    /**
     * Registers necessary JavaScript.
     */
    private function registerGetCombinationScript() {
        $js = <<<JS
                
var addToCartForms = $('.$this->_formClass');
var animDuration = 150;

addToCartForms.change(function(e) {
    var form = $(this);
    var targetName = $(e.target).attr('name');
    
    var sendAjax = ((targetName != 'CartForm[count]') 
        && !(targetName.search('additional_products') != -1) 
        && (targetName.search('CartForm') != -1)
    );
    if(sendAjax) {
        var productId = form.find('#cartform-productid').val();
        var price = form.find('.$this->_priceClass');
        var discountPrice = form.find('.$this->_discountPriceClass');
        var checkedValues = form.find('.field-cartform-attribute_value_id input:checked');
        var selectedValues = form.find('.field-cartform-attribute_value_id option:selected');
        var countInput = form.find('input[name="CartForm[count]"]');
        var submitBtn = form.find('button[type="submit"]');
        var notAvailable = form.find('.$this->_notAvailableMessageClass');
        var sliderThumbs = $('#productImageSliderThumbs');
        var sku = $('.$this->skuCssClass');
        var status = $('.$this->statusCssClass');
    
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
                if (data != 0) {
                    notAvailable.hide("fast");
                    countInput.show(animDuration);
                    discountPrice.show(animDuration);
                    submitBtn.show(animDuration);
                
                    data = JSON.parse(data);
                
                    discountPrice.html(data.newPrice);
                    if (data.oldPrice == data.newPrice) {
                        price.hide(animDuration);
                    } else {
                        price.html(data.oldPrice);
                        price.show(300);
                        price.css('display', 'block');
                    }
                    sku.html(data.sku);
                    status.html(data.availability);
                    $(sliderThumbs).find("img[src='" + data.image + "']").click();
                } else {
                    countInput.hide("fast");
                    discountPrice.hide("fast");
                    price.hide("fast");
                    submitBtn.hide("fast");
                    notAvailable.show("fast");
                }
            },
            error: function (data) {
                console.log(data);
            }
        });
    }
});
JS;
        
        $this->view->registerJs($js, View::POS_END, __CLASS__);
    }
}
